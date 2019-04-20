<script type="text/javascript">
  jQuery(document).on('change', '#variation_id', function() {
    filter_variation_select();
  });
  jQuery( document ).ready(function() {
    if(!jQuery("#variation_id").val()){
      jQuery("#variation_id option:first").remove();
      jQuery("#variation_id").val(jQuery("#variation_id option:first").val());
    }
    filter_variation_select();
  });
  function filter_variation_select() {
    console.log('filter_variation_select');
    var variation_id = jQuery('#variation_id').val();
    var url = "{{ url('filter/standard-filter/variation/variation_options') }}/"+variation_id;
    jQuery.get(url, function(data) {
      if(!jQuery("#variation_option_id").is(':disabled')){
          jQuery("#variation_option_id option").remove();
          jQuery.each(data.options, function(){
              jQuery("#variation_option_id").append('<option value="'+ this.id +'">'+ this.name +'</option>')
          });
      }
      if(!jQuery("#variation_option_id").val()){
        jQuery("#variation_option_id").val(jQuery("#variation_option_id option:first").val());
      }
    });
  }
</script>