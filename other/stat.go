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
	"strings"
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

func validParams(message string) (bool, []string) {
	x := strings.Split(message, ",")
	if len(x[0]) != 64 || len(x[1]) < 1 || len(x[2]) < 1 || !testHash(x[0], x[1], x[2]) {
		return false, x
	}
	return true, x
}

func statUpdate(hash string, x []string) {
	if testMap(hash) {
		mutex.Lock()
		data[hash].Count++
		mutex.Unlock()
	} else {
		mutex.Lock()
		data[hash] = &Release{x[1], x[2], 1}
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
	stat, err := json.Marshal(data)
	if err != nil {
		w.Write([]byte("json error"))
		return
	}
	w.Write([]byte(stat))
}

func wsHandler(w http.ResponseWriter, r *http.Request) {
	conn, err := websocket.Upgrade(w, r, w.Header(), 1024, 1024)
	if err != nil {
		http.Error(w, "Could not open websocket connection", http.StatusBadRequest)
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
			ok, x := validParams(string(message))
			if !ok {
				break
			}
			hash = x[0]
			statUpdate(hash, x)
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
