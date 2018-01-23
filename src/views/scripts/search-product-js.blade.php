<script type="text/javascript"> 
  var typingTimer;                //timer identifier
  var doneTypingInterval = 2000;  //time in ms, 5 second for example
  var $input = $('#barcode');
  var array = [];
  var invoice = false;
  var use_price = 'price';
  $input.on('keydown', function () {
    clearTimeout(typingTimer);
  });
  $input.on('keyup', function () {
    clearTimeout(typingTimer);
    typingTimer = setTimeout(doneTyping, doneTypingInterval);
  });

  function doneTyping () {
    console.log('Terminado de escribir');
    var barcode = $('#barcode').val();
    checkBarcode(barcode);
  }

  $(document).ready(function() {
    $(document).on('change', 'select#search-product', function() { 
      console.log('Producto seleccionado manualmente');
      product_id = $(this).val();
      if(product_id!=''){
        foundProduct(product_id);
        $('select#search-product').val('').change();
      }
    });
  });

  function foundProduct (product_id) {
    window.location.replace("{{ url('admin/search-product') }}/" + product_id);
  }

  function checkBarcode (barcode) {
    $.ajax("{{ url('admin/check-barcode/'.$node->id) }}/" + barcode, {
      success: function(data) {
        //console.log('Exitoso: ' + data);
        if(data.check){
          $('#notification-bar').text('Se agregó el producto "' + barcode +'" correctamente, introduzca la cantidad correcta.');
          foundProduct(data.id);
          $('#barcode').val('');
          console.log('Producto Creado: ' + data.id)
        } else {
          $('#notification-bar').text('No se encontró el producto "' + barcode + '" buscado.');
          console.log('Producto NO Encontrado: ' + data.id)
        }
      },
      error: function() {
        $('#notification-bar').text('Ocurrió un error...');
      }
    });
  }

  $(document).ready(function() {
      var pressed = false; 
      var chars = []; 
      $(window).keypress(function(e) {
          if (e.which >= 48 && e.which <= 57) {
            chars.push(String.fromCharCode(e.which));
          }
          if (pressed == false) {
            setTimeout(function(){
              if (chars.length >= 10) {
                var barcode = chars.join("");
                console.log("Barcode Scanned: " + barcode);
                checkBarcode(barcode);
              }
              chars = [];
              pressed = false;
            },500);
          }
          pressed = true;
      });
  });
  $("#barcode").keypress(function(e){
      if ( e.which === 13 ) {
          console.log("Prevent form submit.");
          e.preventDefault();
      }
  });
</script>