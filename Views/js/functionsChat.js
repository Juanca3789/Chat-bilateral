const baseUrl = "http://localhost/";
function carga() {
  sessionStorage.setItem("idchat", 0);
  let arrMsj = [];
  if (sessionStorage.getItem("id") == null) {
    Swal.fire({
      icon: "info",
      title: "No has iniciado sesión",
      text: "Dirigiendo a pagina principal",
      confirmButtonText: "Ok",
    }).then((result) => {
      if (result.isConfirmed) {
        window.location.assign(baseUrl + "WEB/Views/Pagina/index.html");
      }
    });
  } else {
    obtenerChats(arrMsj);
    repetir(arrMsj);
  }
}

let intervalo2;
function repetir(arrMsj) {
  intervalo2 = setInterval(obtenerChats, 3000, arrMsj);
}

async function obtenerChats(arrMsj) {
  const tabla = document.getElementById("chats");
  let form = new FormData();
  form.append("id", sessionStorage.getItem("id"));
  let resp = await fetch(
    "http://localhost/WEB/Controllers/Chat/ObtenerChats.php",
    {
      method: "POST",
      mode: "cors",
      cache: "no-cache",
      body: form,
    }
  );
  json = await resp.json();
  arr = arrMsj;
  var i = 0;
  //Añadir botones
  json.forEach(async (element) => {
    const fila = document.createElement("ul");
    const columna = document.createElement("button");
    columna.setAttribute("id", "Boton" + element.id);
    if (arr.length < json.length) {
      var nombre = element.Nombre;
      nombre = nombre.charAt(0).toUpperCase() + nombre.slice(1);
      fila.className = "list-group list-group-horizontal";
      columna.className = "list-group-item";
      columna.style = "width: 100%";
      columna.addEventListener("click", () => {
        leerMensajes(element.id, element.contra);
      });
      const Nombre = document.createTextNode(String(nombre));
      Nombre.className = "h3";
      tabla.appendChild(fila);
      fila.appendChild(columna);
      columna.appendChild(Nombre);
      arrMsj.push(element.mensajes);
    } else {
      if (arr[i] != json[i].mensajes) {
        const col = document.getElementById("Boton" + element.id);
        const notificacion = document.createElement("span");
        notificacion.setAttribute("id", "notBot" + element.id);
        notificacion.className =
          "position-absolute top-0 start-100 translate-middle p-2 bg-danger border border-light rounded-circle";
        const text = document.createTextNode("New alerts");
        const span = document.createElement("span");
        span.className = "visually-hidden";
        notificacion.appendChild(span);
        span.appendChild(text);
        col.appendChild(notificacion);
        arr[i] = json[i].mensajes;
      }
    }
    i++;
  });
  if (sessionStorage.getItem("idchat") != 0) {
    if (document.getElementById("notBot" + sessionStorage.getItem("idchat"))) {
      const not = document.getElementById(
        "notBot" + sessionStorage.getItem("idchat")
      );
      leerMensajes(sessionStorage.getItem("idchat"), sessionStorage.getItem("contrasena"));
      not.remove();
    }
  }
}

function nuevoChat() {
  Swal.fire({
    title: "Ingrese el nombre del usuario con el que desea iniciar el chat",
    html: `<input type="text" id="login" class="swal2-input" placeholder="Username">`,
    confirmButtonText: "Sign in",
    focusConfirm: false,
    icon: "info",
    preConfirm: () => {
      const login = Swal.getPopup().querySelector("#login").value;
      if (!login) {
        Swal.showValidationMessage(`Por favor ingrese un nombre`);
      }
      return { login: login };
    },
  }).then((result) => {
    crearChat(result);
  });

  async function crearChat(arr) {
    let form = new FormData();
    form.append("idusuario1", sessionStorage.getItem("id"));
    form.append("usuario2", arr.value.login);
    form.append("contrasena", generarC());
    let resp = await fetch(
      "http://localhost/WEB/Controllers/Chat/NuevoChat.php",
      {
        method: "POST",
        mode: "cors",
        cache: "no-cache",
        body: form,
      }
    );
    json = await resp.json();
    if (json.result == "Chat creado correctamente") {
      Swal.fire({
        title: json.result,
        icon: "success",
        confirmButtonText: "Continuar",
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.reload();
        }
      });
    } else {
      Swal.fire({
        title: json.result,
        icon: "warning",
        confirmButtonText: "Volver a inicio",
      });
    }
  }
}

async function leerMensajes(idchat, contrachat) {
  sessionStorage.setItem("idchat", idchat);
  sessionStorage.setItem("contrasena", contrachat);
  let form = new FormData();
  form.append("idusuario", sessionStorage.getItem("id"));
  form.append("idchat", idchat);
  let resp = await fetch(
    "http://localhost/WEB/Controllers/Mensaje/LeerMensajes.php",
    {
      method: "POST",
      mode: "cors",
      cache: "no-cache",
      body: form,
    }
  );
  json = await resp.json();
  if (json == null) {
    const tabla = document.getElementById("chat");
    tabla.innerHTML = "";
    const fila = document.createElement("tr");
    const Esp1 = document.createElement("td");
    const Esp2 = document.createElement("td");
    const mensaje = document.createElement("td");
    mensaje.className = "celdader";
    const div = document.createElement("div");
    div.className = "textder";
    const span = document.createElement("span");
    span.className = "parr";
    const texto = document.createTextNode(
      "Aquí puedes enviar mensajes presionando el botón 'Enviar'"
    );
    fila.appendChild(Esp1);
    fila.appendChild(Esp2);
    fila.appendChild(mensaje);
    mensaje.appendChild(div);
    div.appendChild(span);
    span.appendChild(texto);
    tabla.appendChild(fila);
  } else {
    const tabla = document.getElementById("chat");
    tabla.innerHTML = "";
    json.forEach(async (element) => {
      if (element.idusuario == sessionStorage.getItem("id")) {
        var T = await desencriptar(sessionStorage.getItem("contrasena"), element.Cuerpo);
        console.log(T);
        const fila = document.createElement("tr");
        const Esp1 = document.createElement("td");
        const Esp2 = document.createElement("td");
        const mensaje = document.createElement("td");
        mensaje.className = "celdader";
        const div = document.createElement("div");
        div.className = "textder";
        const span = document.createElement("span");
        span.className = "parr";
        const texto = document.createTextNode(T);
        fila.appendChild(Esp1);
        fila.appendChild(Esp2);
        fila.appendChild(mensaje);
        mensaje.appendChild(div);
        div.appendChild(span);
        span.appendChild(texto);
        tabla.appendChild(fila);
      } else {
        var T = await desencriptar(sessionStorage.getItem("contrasena"), element.Cuerpo);
        console.log(T);
        const fila = document.createElement("tr");
        const Esp1 = document.createElement("td");
        const Esp2 = document.createElement("td");
        const mensaje = document.createElement("td");
        mensaje.className = "celdaizq";
        const div = document.createElement("div");
        div.className = "textizq";
        const span = document.createElement("span");
        span.className = "parr";
        const texto = document.createTextNode(T);
        fila.appendChild(mensaje);
        fila.appendChild(Esp1);
        fila.appendChild(Esp2);
        mensaje.appendChild(div);
        div.appendChild(span);
        span.appendChild(texto);
        tabla.appendChild(fila);
      }
    });
    if (document.getElementById("notBot" + idchat)) {
      const not = document.getElementById("notBot" + idchat);
      not.remove();
    }
    document.getElementById("contenedor").scrollTop =
      document.getElementById("chat").scrollHeight;
  }
}

async function nuevoMensaje() {
  try {
    let formulario = document.getElementById("Escribir");
    const data = new FormData(formulario);
    let Text = await encriptar(sessionStorage.getItem("contrasena"), data.get("Texto"));
    data.append("idusuario", sessionStorage.getItem("id"));
    data.append("idchat", sessionStorage.getItem("idchat"));
    data.append("Textos", Text);
    let resp = await fetch(
      "http://localhost/WEB/Controllers/Mensaje/NuevoMensaje.php",
      {
        method: "POST",
        mode: "cors",
        cache: "no-cache",
        body: data
      }
    );
    json = await resp.json();
    if (json.result == "Mensaje enviado correctamente") {
      leerMensajes(sessionStorage.getItem("idchat"), sessionStorage.getItem("contrasena"));
      document.getElementById("Escribir").reset();
    }
  } catch (error) {
    console.log(error);
  }
}

function generarC(){
  const cont = "" + aleatorio(0, 10) + aleatorio(0, 10) + aleatorio(0, 10) + aleatorio(0, 10) + aleatorio(0, 10) + aleatorio(0, 10)+ aleatorio(0, 10) + aleatorio(0, 10)+ aleatorio(0, 10) + aleatorio(0, 10);
  return cont;
}

function aleatorio(min, max){
  return Math.floor(Math.random() * (max - min + 1) + min);
}