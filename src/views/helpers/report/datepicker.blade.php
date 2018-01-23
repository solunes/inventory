<script type="text/javascript">
    $(document).ready(function(){
      if($('input#period:checked').val()!='custom'){
          $('#custom-date').hide();
      }
      $('input#period').change(function() {
        if (this.value == 'custom') {
          $('#custom-date').slideDown();
        } else {
          $('#custom-date').slideUp();
        }
      });
      $('.datepicker-initial').pickadate({
        format: 'yyyy-mm-dd',
        formatSubmit: 'yyyy-mm-dd',
        selectYears: 10,
        selectMonths: true,
        min: '{{ $datepicker_initial }}',
        max: '{{ $datepicker_end }}',
      });
      $('.datepicker-end').pickadate({
        format: 'yyyy-mm-dd',
        formatSubmit: 'yyyy-mm-dd',
        selectYears: 10,
        selectMonths: true,
        min: '{{ $datepicker_initial }}',
        max: '{{ $datepicker_end }}',
      });
    });
</script>