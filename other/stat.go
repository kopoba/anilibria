package main

import (
	"crypto/sha256"
	"encoding/hex"
	"encoding/json"
	"log"
	"net/http"
	"strconv"
	"strings"
	"sync"

	"github.com/igm/sockjs-go/sockjs"
)

type Release struct {
	Name, Url string
	Count     int
}

var data = map[string]*Release{}
var mutex sync.RWMutex

func testHash(hash, name, url string) bool {
	h := sha256.New()
	h.Write([]byte(name + url + "secret"))
	if hash == hex.EncodeToString(h.Sum(nil)) {
		return true
	}
	return false
}

func testMap(hash string) bool {
	mutex.RLock()
	_, ok := data[hash]
	mutex.RUnlock()
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
	mutex.RLock()
	x := data[hash].Count
	mutex.RUnlock()
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
	handler := sockjs.NewHandler("/ws", sockjs.DefaultOptions, wsHandler)
	http.Handle("/ws/", handler)
	http.HandleFunc("/stat", webHandler)
	log.Fatal(http.ListenAndServe("localhost:8080", nil))
}

func webHandler(w http.ResponseWriter, r *http.Request) {
	stat, err := json.Marshal(data)
	if err != nil {
		w.Write([]byte("json error"))
		return
	}
	w.Write([]byte(stat))
}

func wsHandler(session sockjs.Session) {
	flag := true
	hash := ""
	for {
		message, err := session.Recv()
		if err != nil {
			statClean(hash)
			break
		}
		if flag {
			ok, x := validParams(message)
			if !ok {
				break
			}
			hash = x[0]
			statUpdate(hash, x)
			flag = false
		}
		if testMap(hash) {
			err = session.Send(strconv.Itoa(getCount(hash)))
			if err != nil {
				statClean(hash)
				break
			}
		}
	}
}
