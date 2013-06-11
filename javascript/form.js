YUI().use('node', function(Y) {

  function init() {
    thesis_hide_supervisors();
  }
  Y.on("domready", init);
});

var thesis_second_fields = '#id_second_supervisor_email, #id_second_supervisor_fname, #id_second_supervisor_sname';
var thesis_second_divs = '#fitem_id_second_supervisor_email, #fitem_id_second_supervisor_fname, #fitem_id_second_supervisor_sname';
var thesis_third_fields = '#id_third_supervisor_email, #id_third_supervisor_fname, #id_third_supervisor_sname';
var thesis_third_divs = '#fitem_id_third_supervisor_email, #fitem_id_third_supervisor_fname, #fitem_id_third_supervisor_sname';

var thesis_hide_supervisors = function() {

  if (!Y.all(thesis_second_fields).get('value').join('')) {
    Y.all(thesis_second_divs).setStyle('display','none');
  }

  if (!Y.all(thesis_third_fields).get('value').join('')) {
    Y.all(thesis_third_divs).setStyle('display','none');
  } else {
    Y.all(thesis_second_divs).setStyle('display','block');
    Y.one('#id_more_supervisors').setStyle('display','none');
  }

}

var thesis_more_supervisors = function() {

  if (Y.all(thesis_second_divs).getStyle('display').pop() == "none") {
    Y.all(thesis_second_divs).setStyle('display','block');
  } else if (Y.all(thesis_third_divs).getStyle('display').pop() == "none") {
    Y.all(thesis_third_divs).setStyle('display','block');
    Y.one('#id_more_supervisors').setStyle('display','none');
  }
}
