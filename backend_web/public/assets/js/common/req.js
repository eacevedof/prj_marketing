const _get_error = error => {
  console.error("from-server:",error)
  return {
    errors: ["Bad response from server"]
  }
}

const _get_response = response => {
  let msg = response?.data?.message
  let code = response?.data?.code
  if(msg && code)
    return {errors:[msg]}

  if (Object.keys(response.data).length>0)
    return response.data

  msg = response?.message
  if(msg)
    return {errors:[msg]}

  msg = response?.errors
  if(msg)
    return {errors: response?.errors}
  return response
}

const reqjs = {
  async get(url) {
    let resp = null
    try {
      resp = await fetch(url, {
        headers: {
          "Accept": "application/json, text/plain, */*",
          "Content-Type": "application/json"
        }
      })
      resp = await resp?.json()
      return _get_response(resp)
    } catch (error) {
      //este error sería del tipo: error.message "Unexpected token < in JSON at position 0"
      return _get_error(error.message)
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
      return _get_response(resp)
    } catch (error) {
      console.log("ERROR:",error)
      return _get_error(error.message)
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
      return _get_response(resp)
    } catch (error) {
      console.log("ERROR:",error)
      return _get_error(error.message)
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
      return _get_response(resp)
    } catch (error) {
      console.log("ERROR:",error)
      return _get_error(error.message)
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
      return _get_response(resp)
    } catch (error) {
      console.log("ERROR:",error)
      return _get_error(error.message)
    }
  }
}

const _get_json = str => {
  try {
    return JSON.parse(str);
  } catch (e) {
    ;
  }
  return null
}

const _get_response_txt = response => {
  //si hay algún error llega en texto plano con lo cual
  //no se puede parsear a response.error
  /*
  * Parse error: syntax error, unexpected token "." in /.../index.php on line 51
  * */
  //console.log("TXT_RESPONSE",response, typeof response)
  if (!response) return ""
  const resp = response.trim()

  //error de compilación
  if (resp.includes("Parse error"))
    return {errors: [resp]}

  //en caso de excepción
  if (resp.includes("{\"")) {
    const json = _get_json(resp)
    if (json!==null && json?.errors) {
      return {errors: json.errors}
    }
  }

  /*
  * si se lanza una exceptcion llega lago como
  * _txt_RESPONSE {"code":500,"status":false,"errors":["eeeeeeerrrror"],"data":[]}
  *
  * {"code":500,"status":false,"errors":["Server throwable error"],"data":[]} string
  * */
  return resp
}

export const reqtxt = {
  async get(url) {
    let resp = null
    try {
      resp = await fetch(url)
      resp = await resp.text()
      return _get_response_txt(resp)
    } catch (error) {
      console.log("TXT_ERROR",error,typeof error)
      return _get_error(error.message)
    }
  },
}

export const get_get = function() {
  const get_urldecoded = str => decodeURIComponent((str+"").replace(/\+/g, "%20"))



  function transformToAssocArray( prmstr ) {
    const params = {};
    var prmarr = prmstr.split("&");
    for ( let i = 0; i < prmarr.length; i++) {
      var tmparr = prmarr[i].split("=");
      params[tmparr[0]] = get_urldecoded(tmparr[1]);
    }
    return params;
  }

  const strquery = window.location.search.substr(1) ?? "";
  if (!strquery) return ""

  const objquery = {}
  strquery.split("&").forEach(strkv => {
    const parts = strkv.split("=")
    objquery[parts[0]] = parts[1]
  })

}

export default reqjs