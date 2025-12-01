var aux;
const baseUrl = "http://localhost/";

function iniciada(){
    if(sessionStorage.getItem("id") != null){
      Swal.fire({
        icon: 'info',
        title: 'Una sesión ya se encuentra activa',
        text: 'Dirigiendo a pagina principal',
        confirmButtonText: 'Ok'
      }
      ).then((result) => {
        if(result.isConfirmed){
          window.location.assign(baseUrl+"WEB/Views/Pagina/pagina.html");
        }
      })
    }
}

function sesion() {
  var botones = document.getElementById("botones");
  var sesion = document.getElementById("Sesion");
  aux = sesion;
  botones.replaceWith(sesion);
  sesion.style.visibility = "visible";
  formSesion();
}

function sesionb() {
  var registro = document.getElementById("Registrate");
  var sesion = document.getElementById("Sesion");
  if (sesion != null) {
    registro.replaceWith(sesion);
    formSesion();
  } else {
    sesion = aux;
    aux = registro;
    registro.replaceWith(sesion);
    formSesion();
  }
  sesion.style.visibility = "visible";
}

function registro() {
  var botones = document.getElementById("botones");
  var registro = document.getElementById("Registrate");
  aux = registro;
  botones.replaceWith(registro);
  registro.style.visibility = "visible";
  formRegistro();
}

function registrob() {
  var sesion = document.getElementById("Sesion");
  var registro = document.getElementById("Registrate");
  if (registro != null) {
    sesion.replaceWith(registro);
    formRegistro();
  } else {
    registro = aux;
    aux = sesion;
    sesion.replaceWith(registro);
    formRegistro();
  }
  registro.style.visibility = "visible";
}

function formSesion() {
  if (document.getElementById("iniciarSesion")) {
    let formulario = document.getElementById("iniciarSesion");
    formulario.onsubmit = function (e) {
      e.preventDefault();
      iniciarSesion();
    };
    async function iniciarSesion() {
      try {
        const data = new FormData(formulario);
        let resp = await fetch(
          "http://localhost/WEB/Controllers/Usuario/Sesion.php",
          {
            method: "POST",
            mode: "cors",
            cache: "no-cache",
            body: data,
          }
        );
        json = await resp.json();
        if (json.id) {
          sessionStorage.setItem("id", json.id);
          sessionStorage.setItem("Nombre", json.Nombre);
          swalSesionA();
        } else {
          swalSesionC();
        }
      } catch (error) {
        console.log(error);
      }
    }
  } else {
    console.log("No se detecta un formulario");
  }
}

function formRegistro() {
  if (document.getElementById("Registro")) {
    let formulario = document.getElementById("Registro");
    formulario.onsubmit = function (e) {
      e.preventDefault();
      iniciarSesion();
    };
    async function iniciarSesion() {
      try {
        const data = new FormData(formulario);
        let resp = await fetch(
          "http://localhost/WEB/Controllers/Usuario/Registro.php",
          {
            method: "POST",
            mode: "cors",
            cache: "no-cache",
            body: data,
          }
        );
        json = await resp.json();
        if ((json.result == "Usuario creado correctamente")) {
          swalRegistroA();
        } else {
          swalRegistroC();
        }
      } catch (error) {
        console.log(error);
      }
    }
  } else {
    console.log("No se detecta un formulario");
  }
}

function swalRegistroA() {
  Swal.fire({
    title: 'Usuario regsitrado exitosamente',
    text: "Por favor, inicie sesión",
    icon: 'success',
    showCancelButton: false,
    confirmButtonText: 'Iniciar Sesión'
  }
  ).then((result) => {
      if (result.isConfirmed){
          window.location.assign(baseUrl+"WEB/Views/Pagina/index.html");
      }
  });
}

function swalRegistroC() {
  Swal.fire({
    title: 'El usuario ingresado ya existe',
    text: "Por favor, inicie sesión",
    icon: 'error',
    showCancelButton: false,
    confirmButtonText: 'Iniciar Sesión'
  }
  ).then((result) => {
      if (result.isConfirmed){
          window.location.assign(baseUrl+"WEB/Views/Pagina/index.html");
      }
  });
}

function swalSesionA(){
    Swal.fire({
      title: 'Sesion iniciada correctamente',
      text: "Dirigiendo a pagina principal",
      icon: 'success',
      showCancelButton: false,
      confirmButtonText: 'Ok'
    }
    ).then((result) => {
        if (result.isConfirmed){
            window.location.assign(baseUrl+"WEB/Views/Pagina/pagina.html");
        }
    });
}

function swalSesionC(){
  Swal.fire({
    title: 'Usuario o contraseña incorrectos',
    text: "Dirigiendo a pagina de inicio",
    icon: 'error',
    showCancelButton: false,
    confirmButtonText: 'Ok'
  }
  ).then((result) => {
      if (result.isConfirmed){
          window.location.assign(baseUrl+"WEB/Views/Pagina/index.html");
      }
  });
}