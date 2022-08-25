const _get_error = (error:string): object => {
  console.error("from-server:",error)
  return {
    errors: ["Bad response from server"]
  }
}

interface IData {
  message: string,
  code: string,
}

interface IResponse {
  data: IData,
  message: string,
  errors: string[],
}

const _get_response = (response: IResponse):object => {
  let msg:string = response?.data?.message
  let code:string = response?.data?.code
  if(msg && code)
    return {errors:[msg]}

  if (Object.keys(response.data).length>0)
    return response.data

  msg = response?.message
  if(msg)
    return {errors:[msg]}

  if(response?.errors)
    return {errors: response?.errors}

  return response
}

const reqjs = {
  async get(url:string): Promise<object|IResponse> {
    let resp: any = null
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
  
  async post(url:string, data:object): Promise<object | IResponse> {
    let resp:any = null
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

  async put(url:string, data:object): Promise<object|IResponse> {
    let resp: any = null
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

  async del(url:string, data:object): Promise<object|IResponse> {
    let resp: any = null
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

  async patch(url:string, data:object): Promise<object|IResponse> {
    let resp: any = null
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
  },

}

export const reqraw = {
  post(path:string, objpayload:object, blank: boolean = true):void {
    const form: HTMLFormElement = document.createElement("form")
    form.method = "post"
    form.action = path
    if (blank) form.target = "_blank"

    for (const key in objpayload) {
      if (objpayload.hasOwnProperty(key)) {
        const hidfield = document.createElement("input")
        hidfield.type = "hidden"
        hidfield.name = `columns[${key}]`
        // @ts-ignore
        hidfield.value = objpayload[key]

        form.appendChild(hidfield)
      }
    }

    document.body.appendChild(form)
    form.submit()
  },
}

const _get_json = (str: string): object |null => {
  try {
    return JSON.parse(str);
  } catch (e) {
    ;
  }
  return null
}

const _get_response_txt = (response: string): object|null|string => {
  //si hay algún error llega en texto plano con lo cual
  //no se puede parsear a response.error
  /*
  * Parse error: syntax error, unexpected token "." in /.../index.php on line 51
  * */
  //console.log("TXT_RESPONSE",response, typeof response)
  if (!response) return null
  const resp:string = response.trim()

  //error de compilación
  if (resp.includes("Parse error"))
    return {errors: [resp]}

  //en caso de excepción
  if (resp.includes("{\"")) {
    const json:object|null = _get_json(resp)
    // @ts-ignore
    if (json!==null && json?.errors) {
      // @ts-ignore
      return {errors: json.errors}
    }
  }

  /*
  * si se lanza una exceptcion llega algo como
  * {"code":500,"status":false,"errors":["eeeeeeerrrror"],"data":[]}
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
      console.log("reqtxt.error",error,typeof error)
      return _get_error(error.message)
    }
  },
}

export const is_get_200 = async url => {
  try {
    const resp = await fetch(url)
    return parseInt(resp.status) === 200
  } catch (error) {
    return false
  }
}

/*
esto me lleva la query al infinito
export const get_urlparams = () => {
  const strquery = window.location.search.substr(1) ?? "";
  if (!strquery) return ""

  const get_urldecoded = str => decodeURIComponent((str+"").replace(/\+/g, "%20"))

  const objquery = {}
  strquery.split("&").forEach(strkv => {
    const parts = strkv.split("=")
    objquery[parts[0]] = get_urldecoded(parts[1])
  })
  return objquery
}
*/

export default reqjs