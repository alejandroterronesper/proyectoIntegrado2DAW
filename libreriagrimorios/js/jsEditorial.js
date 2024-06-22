

window.comunicarVentanaMain = function (arrayDatos) {

    borraElementosNodo(document.getElementById("editorial"));
    document.getElementsByTagName("body")[0].style.marginTop = "20%";


   let divContenedor = creaNodo("div", null, {"class": "divCRUDEditorial"});

   let nombre = creaNodo ("h4", arrayDatos[0], []);

   let historia = creaNodo ("textarea", arrayDatos[1] , { "readonly":true,  "rows": 10,"cols":70,  "name": "historia",
                                                        "id":"historia"})

   divContenedor.appendChild(nombre);
   divContenedor.appendChild(historia);
   document.getElementById("editorial").appendChild(divContenedor);
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