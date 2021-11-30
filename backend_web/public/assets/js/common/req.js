const get_error = error => ({error:error})

export const is_2xx = resp => {
  let r = resp?.data?.code ?? null
  if (!r) return true
  r = parseInt(r)
  return (r>199 && r<300)
}

/*
* Excepciones controladas por el back (controlador):
status-code:404
data:
  code: 404
  message: "Resource not found"

* Excepciones globales
status-code: 500 por lo general o xxx si existe
code: 500
  data: []
  errors: ["request method POST not allowed"]
  0: "request method POST not allowed"
  status: false

Errores de validacion
status-code: xxx
* data: []
errors: [{…}]
included: []
links: []
message: ""
status: false
*
* Errores de compilacion back
status-code:200

error: SyntaxError: Unexpected token < in JSON at position 0
  message: "Unexpected token < in JSON at position 0"
  stack: "SyntaxError: Unexpected token < in JSON at position 0"
* */

const injson = {
  async get(url) {
    let resp = null
    try {
      resp = await fetch(url, {
        method: "POST",
        headers: {
          "Accept": "application/json, text/plain, */*",
          "Content-Type": "application/json"
        }
      })
      resp = await resp?.json()
      return resp
    } catch (error) {
      //este error sería del tipo: error.message "Unexpected token < in JSON at position 0"
      return get_error(error)
    }
  },
  
  async post(url, data) {
    let resp = null
    try {
      resp = await fetch(url, {
        method: "POST",
        headers: {
          "Accept": "application/json, text/plain, */*",
          "Content-Type": "application/json"
        },
        body: JSON.stringify(data)
      })
      resp = await resp?.json()
      return resp
    } catch (error) {
      console.log("ERROR:",error)
      return get_error(error)
    }
  },

  async put(url, data) {
    let resp = null
    try {
      resp = await fetch(url, {
        method: "PUT",
        headers: {
          "Accept": "application/json, text/plain, */*",
          "Content-Type": "application/json"
        },
        body: JSON.stringify(data)
      })
      resp = await resp?.json()
      return resp
    } catch (error) {
      console.log("ERROR:",error)
      return get_error(error)
    }
  },

  async del(url, data) {
    let resp = null
    try {
      resp = await fetch(url, {
        method: "DELETE",
        headers: {
          "Accept": "application/json, text/plain, */*",
          "Content-Type": "application/json"
        },
        body: JSON.stringify(data)
      })
      resp = await resp?.json()
      return resp
    } catch (error) {
      console.log("ERROR:",error)
      return get_error(error)
    }
  },

  async patch(url, data) {
    let resp = null
    try {
      resp = await fetch(url, {
        method: "PATCH",
        headers: {
          "Accept": "application/json, text/plain, */*",
          "Content-Type": "application/json"
        },
        body: JSON.stringify(data)
      })
      resp = await resp?.json()
      return resp
    } catch (error) {
      console.log("ERROR:",error)
      return get_error(error)
    }
  }
}

export default injson