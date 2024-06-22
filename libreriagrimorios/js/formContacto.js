var formularioContacto = document.getElementById("formularioJS");//le hacemos un even prevent default

formularioContacto.addEventListener("submit", function(event){
    event.preventDefault();
})


if (!("Notification" in window)) {
    alert("Este navegador no soporta las notificaciones del sistema");
   }
   
else {
    Notification.requestPermission().then(function(result) {
    console.log(result);
    });
}




/**
 * Al pulsar el botón se van a validar los diferentes campos
 * con expresiones regulares, si todo sale bien 
 * se envia una notificación, si no, se muestran los diferentes errores
 */
document.getElementById("botonJS").onclick = function (){
    var divError = document.getElementById("divSugERROR");
    borraElementosNodo(divError);

    //recogemos datos formulario
    let nombre = document.getElementById("nombre");
    let telefono = document.getElementById("telefono")
    let email = document.getElementById("correo");
    let fecha = document.getElementById("fechaNac");
    let estudios = document.getElementById("estudios");
    let sugerencia = document.getElementById("sugerencia");

    var arrayErrores = []


    //Se valida el nombre
    if (nombre.value === ""){


        arrayErrores.push("El campo de nombre no puede ir vacio")
    }

    if (nombre.value.length > 24){
        arrayErrores.push("El campo de nombre solo puede tener una longitud de hasta 24 caracteres")
   
    }


    //TELEFONO
    telefonoEntero = parseInt(telefono.value);

    if (isNaN(telefonoEntero) === true){

        arrayErrores.push("El telefono debe ser un número no una cadena");
    }

    //Validamos telefono, se comprueba que tenga 9 dígitos
    //empiece por 952 continue con 84 o 70 y el resto de números
    const regExpTelefonoAntequera =  new RegExp ("^[952]{3}([84]|[70])[0-9]{4}");
    if (!regExpTelefonoAntequera.test(telefono.value)){

            arrayErrores.push("Tienes que introducir un telefono de la comarca de Antequera 952 84/70 XX XX")
     
  

    }


    //Email
    if (email.value === ""){
        arrayErrores["email"] = []
        arrayErrores.push("Debes introducir un correo");

    }


    const regExpEmail = new RegExp("^[a-zA-Z0-9-_\.]+@{1}[a-z]+\.[a-z]{2,3}$");

    if (!regExpEmail.test(email.value)){
      
            arrayErrores.push("Formato de email incorrecto debe ser email@email.com, debe incluir alguna _");

        


    }



    //fecha nos llega por input tipo date
    if (fecha.value === ""){

        arrayErrores.push("Debes introducir una fecha");

    }

    //se comprueba si es mayor de edad
    let fechaDate = new Date (fecha.value);
    let fechaHoy = new Date ();
    let conversorAnios = 1000*60*60*24*365

    let resultado = Number.parseInt(fechaHoy.getTime() - fechaDate.getTime())/conversorAnios;

    if (resultado < 18){
   
            arrayErrores.push("Debes ser mayor de edad");

        

    }


    //Estudios
  

    if (estudios.selectedIndex === ""){

        arrayErrores.push("Debes elegir una opción distinta a la de por defecto en el nivel de estudios");

    }




    //sugerencia
    if (sugerencia.value === ""){
  
        arrayErrores.push("El campo de sugerencia no puede ir vacio");

    }

    if (sugerencia.value.length > 220){


            arrayErrores.push("La sugerencia debe contener hasta 220 caracteres");

        
       


    }


    var errores = arrayErrores.length
    //Comprobamos si hay errores 
    if (errores !== 0){
        
        divError.style.display = "block";
        for(let error of arrayErrores){



            let errorP = creaNodo ("p",error);
            divError.appendChild(errorP);
        }


    }
    else{
        divError.style.display = "none";

        //Limpiamos los campos
        nombre.value = "";
        telefono.value = "";
        email.value = "";
        fecha.value ="";
        estudios.selectedIndex = 0;
        sugerencia.value = "";


        //Enviamos notifiación de que se ha validado
        var options = {
            body: "Sugerencia enviada! ", // Mensaje.
            icon: "../imagenes/logo.jpg" // Icono de la notificación (opcional).
        }
        var n = new Notification("Librería grimorios", options);
        setTimeout(n.close.bind(n), 5000); 
    }


}


/**
 * Funcion que recibe como parámetro un nodo
 * y borra todos los elementos que tenga dentro
 * 
 * @param {Node} nodo 
 */
function borraElementosNodo (nodo){
    while (nodo.firstChild) {
        nodo.removeChild(nodo.firstChild);
    }
}

/**
 * Funcion que permite crear un nodo,
 * se le pasa como parametro una cadena que indica la etiqueta
 * una cadena que es el contenido que va dentro de la etiqueta
 * y un array con las diferentes propiedades del nodo
 * @param {String} tipoNodo 
 * @param {String | Null} contenido 
 * @param {Array | Null} atributos 
 * @returns {Node}
 */
function creaNodo (tipoNodo, contenido = null, atributos = null){

    let nodo = document.createElement(tipoNodo);

    if (contenido !== null){
        let addContenido = document.createTextNode(contenido);
        nodo.appendChild(addContenido);
    }

    if (atributos !== null){

        for(const clave in atributos){
            nodo.setAttribute(clave, atributos[clave]);
        }
    }

    return nodo;
}
