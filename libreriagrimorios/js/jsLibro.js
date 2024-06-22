/**
 * Función que nos permite recibir un mensaje de la ventana principal
 * y mostrarla por pantalla
 * @param {Object} libro 
 */
window.comunicarVentanaPrincipal = function (libro) {


   //borramos posibles elementos del nodo
   borraElementosNodo(document.getElementById("libro"));



   document.getElementsByTagName("body")[0].style.marginTop = "20%";

   let divContenedor = creaNodo("div", null, {"class": "divCRUDInfo"});


   let titulo = creaNodo("label", "Nombre: ", {"for" : "nombre"});
   let tituloInput = creaNodo("input", null, {"type" : "text", "name" : "nombre", "value": libro.titulo, "readonly": true});


   let isbn = creaNodo("label", "ISBN: ", {"for" : "isbn"});
   let isbnInput = creaNodo("input", null, {"name" : "isbn", "value": libro.isbn, "type" : "text", "readonly": true});


   let autor =  creaNodo("label", "Autor: ", {"for" : "autor"});
   let autorInput = creaNodo("input", null, {"name" : "autor", "value": libro.autor, "type" : "text", "readonly": true})

   let genero = creaNodo("label", "Género: ", {"for" : "genero"});
   let generoInput = creaNodo("input", null, {"name" : "autor", "value": libro.genero, "type" : "text", "readonly": true})

   let editorial = creaNodo("label", "Editorial: ", {"for" : "editorial"});
   let editorialInput = creaNodo("input", null, {"name" : "autor", "value": libro.editorial, "type" : "text", "readonly": true})

    let fecha = creaNodo("label", "Fecha de lanzamiento: ", {"for" : "editorial"});
    let fechaInput = creaNodo("input", null, {"name" : "autor", "value": pasarAfechaNormal(libro.fecha_lanzamiento), "type" : "text", "readonly": true})

   let precio = creaNodo("label", "Precio: ", {"for" : "precio"});
   let precioInput = creaNodo("input", null, {"name" : "autor", "value": libro.precio_venta, "type" : "text", "readonly": true})


   let foto = creaNodo ("img", null, {"alt": "libro " + libro.titulo, "src" : "../imagenes/libros/" + libro.foto })


   divContenedor.appendChild(titulo)
   divContenedor.appendChild(tituloInput)
   divContenedor.appendChild(creaNodo("br"));


   divContenedor.appendChild(isbn)
   divContenedor.appendChild(isbnInput)
   divContenedor.appendChild(creaNodo("br"));


   divContenedor.appendChild(autor)
   divContenedor.appendChild(autorInput)
   divContenedor.appendChild(creaNodo("br"));


   divContenedor.appendChild(genero)
   divContenedor.appendChild(generoInput)
   divContenedor.appendChild(creaNodo("br"));


   divContenedor.appendChild(editorial)
   divContenedor.appendChild(editorialInput)
   divContenedor.appendChild(creaNodo("br"));


   divContenedor.appendChild(fecha)
   divContenedor.appendChild(fechaInput)
   divContenedor.appendChild(creaNodo("br"));


   divContenedor.appendChild(precio)
   divContenedor.appendChild(precioInput)


   let divImagen = creaNodo ("div", null, {"class" : "divCRUDImg"});
   divImagen.appendChild(foto);


   document.getElementById("libro").appendChild(divContenedor);
   document.getElementById("libro").appendChild(divImagen);
   


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
 * Función para convertir un fecha de SQL
 * de formato yyyy-mm-dd a formato normal
 * dd/mm/aaaa
 * @param {String} fecha cadena SQL
 * @returns {String} devuelve fecha formateada
 */
function pasarAfechaNormal (fecha){

    arrayFecha = fecha.split("-");

    return arrayFecha[2] + "/" + arrayFecha[1] + "/" + arrayFecha[0]
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