/*JAVASCRIPT */


/**
 * Comprobamos notificaciones
 */
if (!("Notification" in window)) {
    alert("Este navegador no soporta las notificaciones del sistema");
   }
   
else {
    Notification.requestPermission().then(function(result) {
    console.log(result);
    });
}





/**
 * --EVENTOS USADOS--
 *      - onclick
 *      - mouson
 *      - mouseover
*/

var tituloCabecera = document.getElementsByClassName("nombreTienda")[0];

/**
 * Evento para la cabecera de la página, al pasar el ratón por encima
 * se modificaran las propiedades css, se le añadira un color rojo
 * una sombra al texto gris y negra y una transición de 2 segundos
 * 
 */
tituloCabecera.onmouseover = function (){
    
    tituloCabecera.style.color = "#7c1212"; //modificación de CSS
    tituloCabecera.style.textShadow = "5px 5px 1px grey, 10px 10px 1px black";
    tituloCabecera.style.transition = "all 2s";
}


/**
 * Evento para la cabecera de la página, cuando el botón deja
 * de estar encima del título, se modifican las propiedades establecidas cuando
 * se ha ganado el foco y lo ponemos como estaba al principio de cuando
 * se carga la página, sin sobreado y sin color rojo, se mantiene la transición
 * para que el cambio no sea tan brusco
 */
tituloCabecera.onmouseout = function (){
    tituloCabecera.style.color = "black"; //modificación de CSS
    tituloCabecera.style.textShadow = "none"
    tituloCabecera.style.transition = "all 2s";

}




//-------------------------------------------------------------------------------------------------------------//
//-----------------------------------------------FETCH------------------------------------------------------//
//-------------------------------------------------------------------------------------------------------------//
/**
 * Función que se llama cuando se pulsa el botón de ver Información de los libros
 * en la página de Index, al pulsar este botón, se abrirá un ventana secundaría para
 * mostrar más datos del libro del que se ha pulsado el botón
 * @param {Integer} codigoLibro 
 */
function verInformacionLibro(codigoLibro){

    
    //convertimos el valor
    codigoLibro = parseInt(codigoLibro); //lo parseamos

    

    //comprobamos que el código sea distinto de 0
    //Para hacerse la petición el número debe ser distinto de 0

    if (codigoLibro !== 0){
        
        var dominio = document.location.protocol + "//" + document.location.hostname;
        var URL = dominio + "/libros/PeticionAJAXLibro?id=" + codigoLibro;

        fetch (URL, {
            method: "GET",
            headers: {"Content-Type":"application/x-www-form-urlencoded"}, // Poner solo si se envían datos
        })
        .then (function (response){

            if (response.ok){
                response.text()
                .then (function (resp){

                    var arrayJSON = JSON.parse(resp);
                    

                    if (arrayJSON["correcto"] === true){ //validamos resultado

        
                        //Enviamos el libro seleccionado a la función para crear la ventana
                        sacarVentanaLibro(arrayJSON["libro"])

                    }
                    else{
                        console.log(arrayJSON["libro"]);

                    }
                })
            }
        })
        .catch(function(e){ //En caso de haber errores
            console.log("Error: " + e);
        })
    }
    else{
        console.log("Error el código debe ser distinto de 0");
    }
}



/**
 * Función para enviar un objeto Libro
 * al js de la ventana hija para asi poder
 * mostrar la información en una ventana auxiliar
 * @param {Object} libro 
 */
function sacarVentanaLibro (libro){


    let ventanaLibro = window.open("./js/ventanaLibro.html", 'Ventana', 'height=800, width= 1000')
    ventanaLibro.moveTo((screen.width / 2 - 800), (screen.height) /2 - 1000)
    ventanaLibro.addEventListener("DOMContentLoaded", function () { 
        ventanaLibro.comunicarVentanaPrincipal(libro);
    }
    );

}




/**
 * Función del para mostrar en una ventana emergente
 * la historia de editorial seleccionada desde el Crud de editoriales
 * 
 * @param {Integer} codEditorial 
 */
function verDescripcionEditorial (codEditorial){
 
   let codigoEditorial = parseInt(codEditorial);
   
   
   if (codEditorial !== 0){

    var dominio = document.location.protocol + "//" + document.location.hostname;
    var URL = dominio + "/api/EditorialAPI?cod_editorial=" + codigoEditorial;


    fetch (URL, {
        method: "GET",
        headers: {"Content-Type":"application/x-www-form-urlencoded"}, // Poner solo si se envían datos
    })
    .then (function (response){
        if (response.ok){
            response.text()
            .then (function (resp){

                var arrayJSON = JSON.parse(resp);


                if (arrayJSON["correcto"] === true){ //Se valida resultado 

                    let nombreEditorial = arrayJSON["datos"][0].nombre
                   
                    let historiaEditorial = arrayJSON["datos"][0].historia
                 
                    let arrayDatos = [];

                    arrayDatos.push(nombreEditorial);
                    arrayDatos.push(historiaEditorial);

                    sacarVentanaHistoria (arrayDatos);
                    
                }
                else{
                    console.log("ERROR: " + arrayJSON["datos"])
                }


           
            })
            .catch (function (e){
                console.log("Error: " + e)
            })
        }
    })
    .catch (function (e){
        console.log("Error: " + e)
    })
    

   }
   else{
    console.log("El código de la editorial debe ser distinto de 0")
   }
    
    
}


/**
 * 
 * @param {} arrayDatos 
 */
function sacarVentanaHistoria (arrayDatos){


    let ventanaHistoriaEd =  window.open("../js/ventanaEditorial.html", 'Ventana', 'height=800, width=800');
    ventanaHistoriaEd.moveTo((screen.width / 2), (screen.height) /2 )
    ventanaHistoriaEd.addEventListener("DOMContentLoaded", function () { 
        ventanaHistoriaEd.comunicarVentanaMain(arrayDatos);
    }
    );

}




//---------------------------------------------------------------------------------------------------//
//----------------------------------------------COOKIES----------------------------------------------//
//---------------------------------------------------------------------------------------------------//
var enlaceLogin = "http://www.libreriagrimorios.es" + "/login/DatosLogin"


fetch (enlaceLogin, {
    method: "GET",
    headers: {"Content-Type":"application/x-www-form-urlencoded"}, // Poner solo si se envían datos //    headers: {}, // Poner solo si se envían datos
})
.then (function (response){

    if (response.ok){
        response.text()
        .then (function (resp){

            var arrayJSON = JSON.parse(resp);
            

            console.log(arrayJSON)

            if (arrayJSON["correcto"] === true){ //Ha habido inicio de sesión
            
                //guardamos en Cookies durante 3 minutos los datos de login
                let nick = arrayJSON["datos"]["nick"];
                let pw = arrayJSON["datos"]["pw"];
                var diaHoy = new Date ();
                const fechaCookie = new Date (diaHoy.getTime()  + (3*60*1000)) //se guarda solo 3 minutos

                if (comprobarCookie("nick") === true){ //Se actualiza
    
    
                
                }

                if (comprobarCookie("nick") === false){ //Se crea
                    document.cookie = "nick=" + nick + ";expires="+fechaCookie.toUTCString() + "  ; path=/login";
     

                }


                if (comprobarCookie("pw") === true){ //Se actualiza

                }


                if (comprobarCookie("pw") === false){ //Se  crea
                    document.cookie = "pw=" + pw + ";expires="+fechaCookie.toUTCString() + "  ; path=/login";

                }


                var loginPag =  window.location.href;

                if (loginPag === "http://www.libreriagrimorios.es/login/InicioSesion"){

                    //ponemos el valor de las cookies en los campos
                    let nickValue = obtenerCookie("nick");
                    let pwValue = obtenerCookie("pw");
                    document.getElementById("login_nick").value = nickValue
                    document.getElementById("login_contrasenia").value = pwValue

            
                }

            }
        })
    }
})
.catch(function(e){ //En caso de haber errores
    console.log("Error: " + e);
})



//-------------------------------------------------------------------------------------------------------------//
//-------------------------------------------------CLASES------------------------------------------------------//
//-------------------------------------------------------------------------------------------------------------//

/**
 * CLASE LOGIN
 */
class login {


    //Variables de instancia
    #nick
    #contrasenia


    constructor (nick, contrasenia){

    }




    //getters y setters


    /**
     * Método get numPuertas
     * @returns {String}
     */
    getNick() {
        return this.#nick;
    }


    /**
     * Método que devuelve revision
     * @returns {String}
     */
    getContrasenia() {
        return this.#contrasenia;
    }


    /**
     * método para modificar puertas
     * @param {Int} puertas 
     */
    setNick(puertas) {


        this.#nick = puertas;
    }


    /**
     * Método para cambiar la revision
     * @param {Boolean} revision 
     */
    setContrasenia(revision) {
        this.#contrasenia = revision
    }
    




}


//--------------------FUNCIONES--------------------------------//
function obtenerCookie(clave) {
    var name = clave + "=";
    var ca = document.cookie.split(';'); // Obtenemos los campos de la cookie
    for(var i=0; i<ca.length; i++) {
    var c = ca[i];
    while (c.charAt(0)==' ')
    c = c.substring(1); // Eliminamos los espacios en blanco
    if (c.indexOf(name) == 0)
    return c.substring(name.length,c.length);
    }
    return "";
}

function comprobarCookie(clave) {
    var clave = obtenerCookie(clave);
    if (clave != "") {
    // La cookie existe.
    return true
    }
    else {
    // La cookie no existe.
    return false;
    }
   }