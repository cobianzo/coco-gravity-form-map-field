
- ✅ Mostrar una preview del poligono/marker en la lista de entries
- Anadir UNDO al crear el poligono
- Check, in render.php, where I load the google maps API, ig that API has not been already loaded before.
If it's loaded twise we receive an error. If it has been alreayd loaded, we still need to ensure that the 
libraries places,drawing,marker are loaded too.
- Translate texts (using wp cli to create the .pot and AI to translate the .po and generate the .mo with poEdit.).

# TESTS

- Verificar que el valor por defecto funciona tanto en Marker como en Polygon
- Verificar que el valor se mantiene al recargar la página debido a un error en el form
