package main

import (
	"crypto/sha256"
	"encoding/hex"
	"encoding/json"
	"log"
	"net"
	"net/http"
	"os"
	"strconv"
	"sync"

	"github.com/gorilla/websocket"
)

type Release struct {
	Name, Url string
	Count     int
}

var data = map[string]*Release{}
var mutex sync.Mutex

func testHash(hash, name, url string) bool {
	h := sha256.New()
	h.Write([]byte(name + url + "secret"))
	if hash == hex.EncodeToString(h.Sum(nil)) {
		return true
	}
	return false
}

func testMap(hash string) bool {
	mutex.Lock()
	_, ok := data[hash]
	mutex.Unlock()
	return ok
}

func statClean(hash string) {
	if len(hash) > 0 {
		if testMap(hash) {
			if getCount(hash) > 0 {
				mutex.Lock()
				data[hash].Count--
				mutex.Unlock()
			}
			if getCount(hash) == 0 {
				mutex.Lock()
				delete(data, hash)
				mutex.Unlock()
			}
		}
	}
}

func getCount(hash string) int {
	mutex.Lock()
	x := data[hash].Count
	mutex.Unlock()
	return x
}

func validParams(message []byte) (bool, map[string]string) {
	var x map[string]string
	if err := json.Unmarshal(message, &x); err != nil {
		return false, x
	}
	if len(x["Hash"]) != 64 || len(x["Name"]) < 1 || len(x["Url"]) < 1 || !testHash(x["Hash"], x["Name"], x["Url"]) {
		return false, x
	}
	return true, x
}

func statUpdate(x map[string]string) {
	if testMap(x["Hash"]) {
		mutex.Lock()
		data[x["Hash"]].Count++
		mutex.Unlock()
	} else {
		mutex.Lock()
		data[x["Hash"]] = &Release{x["Name"], x["Url"], 1}
		mutex.Unlock()
	}
}

func main() {
	http.HandleFunc("/ws/", wsHandler)
	http.HandleFunc("/stat/", webHandler)
	const SOCK = "/tmp/stat.sock"
	os.Remove(SOCK)
	unixListener, err := net.Listen("unix", SOCK)
	if err != nil {
		log.Fatal("Listen (UNIX socket): ", err)
	}
	defer unixListener.Close()
	os.Chmod(SOCK, 0777) // os.Chown(SOCK, 1001, 33) not work (operation not permitted)
	log.Fatal(http.Serve(unixListener, nil))
}

func webHandler(w http.ResponseWriter, r *http.Request) {
	mutex.Lock()
	stat, err := json.Marshal(data)
	mutex.Unlock()
	if err != nil || len(stat) == 0 {
		w.Write([]byte("json error"))
		return
	}
	w.Write([]byte(stat))
}

func wsHandler(w http.ResponseWriter, r *http.Request) {
	conn, err := websocket.Upgrade(w, r, w.Header(), 1024, 1024)
	if err != nil {
		return
	}
	flag := true
	hash := ""
	defer conn.Close()
	for {
		mt, message, err := conn.ReadMessage()
		if err != nil {
			statClean(hash)
			break
		}
		if flag {
			ok, x := validParams(message)
			if !ok {
				break
			}
			hash = x["Hash"]
			statUpdate(x)
			flag = false
		}
		if testMap(hash) {
			err = conn.WriteMessage(mt, []byte(strconv.Itoa(getCount(hash))))
			if err != nil {
				statClean(hash)
				break
			}
		}
	}
}
