YUI().use('node', function(Y) {

  function init() {
    thesis_hide_supervisors();
  }
  Y.on("domready", init);
});

var thesis_second_fields = '#id_second_supervisor_email, #id_second_supervisor_fname, #id_second_supervisor_sname';
var thesis_third_fields = '#id_third_supervisor_email, #id_third_supervisor_fname, #id_third_supervisor_sname';
var supervisor_button_id = '#id_more_supervisors';

var thesis_hide_supervisors = function() {

  if (!Y.all(thesis_second_fields).get('value').join('')) {
    $(thesis_second_fields).closest('.fitem').css('display', 'none');
  }

  if (!Y.all(thesis_third_fields).get('value').join('')) {
    $(thesis_third_fields).closest('.fitem').css('display', 'none');
  } else {
    $(thesis_second_fields).closest('.fitem').css('display', 'block');
    $(supervisor_button_id).css('display','none');
  }

};

var thesis_more_supervisors = function() {

  if ($(thesis_second_fields).closest('.fitem').css('display') == "none") {
    $(thesis_second_fields).closest('.fitem').css('display', 'block');
  } else if ( $(thesis_third_fields).closest('.fitem').css('display') == "none") {
    $(thesis_third_fields).closest('.fitem').css('display', 'block');
    $(supervisor_button_id).css('display','none');
  }
};
