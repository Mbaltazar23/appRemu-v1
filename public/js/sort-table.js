let sortDirection = {}; // Objeto para almacenar la dirección de ordenamiento por columna

function sortTable(columnIndex) {
  var table = document.getElementById("dataTable");
  var rows = Array.from(table.rows).slice(1);
  var switching = true;
  var shouldSwitch, i;

  // Inicializa la dirección si es la primera vez que se ordena esta columna
  if (sortDirection[columnIndex] === undefined) {
    sortDirection[columnIndex] = true; // true para ascendente
  } else {
    sortDirection[columnIndex] = !sortDirection[columnIndex]; // Cambia la dirección
  }

  while (switching) {
    switching = false;
    for (i = 0; i < rows.length - 1; i++) {
      shouldSwitch = false;
      var x = rows[i].getElementsByTagName("TD")[columnIndex].innerHTML;
      var y = rows[i + 1].getElementsByTagName("TD")[columnIndex].innerHTML;

      // Compara dependiendo de la dirección de ordenamiento
      if (sortDirection[columnIndex] ? x.localeCompare(y, 'en', { numeric: true }) > 0 : x.localeCompare(y, 'en', { numeric: true }) < 0) {
        shouldSwitch = true;
        break;
      }
    }
    if (shouldSwitch) {
      var tempRow = rows[i].innerHTML;
      rows[i].innerHTML = rows[i + 1].innerHTML;
      rows[i + 1].innerHTML = tempRow;
      switching = true;
    }
  }
}
