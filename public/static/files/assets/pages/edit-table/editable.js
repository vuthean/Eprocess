  'use strict';
 $(document).ready(function() {
     $("#example-1").on('click', '.btnDelete', function () {
         $(this).closest('tr').remove();
     });
     $("#example-3").on('click', '.btnDelete', function () {
         $(this).closest('tr').remove();
     });
     $("#example-4").on('click', '.btnDelete', function () {
        $(this).closest('tr').remove();
    });
     $("#table_bpv").on('click', '.btnDelete', function () {
         $(this).closest('tr').remove();
     });
     $("#table_brv").on('click', '.btnDelete', function () {
         $(this).closest('tr').remove();
     });
     $("#table_jv").on('click', '.btnDelete', function () {
         $(this).closest('tr').remove();
     });
     $("#table_crv").on('click', '.btnDelete', function () {
         $(this).closest('tr').remove();
     });
     $("#table_cpv").on('click', '.btnDelete', function () {
         $(this).closest('tr').remove();
     });
    $('#example-2').Tabledit({

        columns: {

          identifier: [0, 'id'],
          editable: [[1, 'First Name'], [2, 'Last Name']]

      }

  });
});

  // add_row_segment
  // <<<>>>>
 function add_row_2(){
     var table = document.getElementById("example-3");
     // var numberoftds = document.getElementById("example-1").rows.length;
     var t1=(table.rows.length);
     var row = table.insertRow(t1);
     var cell0 = row.insertCell(0);
     var cell1 = row.insertCell(1);
     var cell2 = row.insertCell(2);

     cell0.className='abc';
     cell1.className='abc';
     cell2.className='abc';


     $('<textarea cols="30" rows="1" class="tabledit-input form-control input-sm" type="text" name="vendor_name[]"   ></textarea>').appendTo(cell0);
     $('<textarea cols="30" rows="1" class="tabledit-input form-control input-sm" type="text" name="vendor_description[]"   ></textarea>').appendTo(cell1);
     $('<i class="fa fa-times btnDelete" style="font-size: 20px;color: red"></i>\n').appendTo(cell2);

 }
 function add_row_update(){
    var table = document.getElementById("example-4");
    // var numberoftds = document.getElementById("example-1").rows.length;
    var t1=(table.rows.length);
    var row = table.insertRow(t1);
    var cell0 = row.insertCell(0);
    var cell1 = row.insertCell(1);
    var cell2 = row.insertCell(2);

    cell0.className='abc';
    cell1.className='abc';
    cell2.className='abc';


    $('<textarea cols="30" rows="1" class="tabledit-input form-control input-sm" type="text" name="vendor_name_update[]"   ></textarea>').appendTo(cell0);
    $('<textarea cols="30" rows="1" class="tabledit-input form-control input-sm" type="text" name="vendor_description_update[]"   ></textarea>').appendTo(cell1);
    $('<i class="fa fa-times btnDelete" style="font-size: 20px;color: red"></i>\n').appendTo(cell2);

}
  function table_brv(){
      var table = document.getElementById("table_brv");
      // var numberoftds = document.getElementById("example-1").rows.length;
      var t1=(table.rows.length);
      var row = table.insertRow(t1);
      var cell0 = row.insertCell(0);
      var cell1 = row.insertCell(1);
      var cell2 = row.insertCell(2);
      var cell3 = row.insertCell(3);
      var cell4 = row.insertCell(4);
      var cell5 = row.insertCell(5);
      var cell6 = row.insertCell(6);
      var cell7 = row.insertCell(7);
      var cell8 = row.insertCell(8);
      var cell9 = row.insertCell(9);
      var cell10 = row.insertCell(10);
      var cell11 = row.insertCell(11);
      var cell12 = row.insertCell(12);
      cell0.className='abc';
      cell1.className='abc';
      cell2.className='abc';
      cell3.className='abc';
      cell4.className='abc';
      cell5.className='abc';
      cell6.className='abc';
      cell7.className='abc';
      cell8.className='abc';
      cell9.className='abc';
      cell10.className='abc';
      cell11.className='abc';
      cell12.className='abc';

      $('<textarea class="tabledit-input form-control input-sm resizetext" type="text" name="Last"   ></textarea>').appendTo(cell0);
      $('<textarea class="tabledit-input form-control input-sm resizetext" type="text" name="Last"   ></textarea>').appendTo(cell1);
      $('<textarea class="tabledit-input form-control input-sm resizetext" type="text" name="Last"   ></textarea>').appendTo(cell2);
      $('<textarea class="tabledit-input form-control input-sm resizetext" type="text" name="Last"   ></textarea>').appendTo(cell3);
      $('<textarea class="tabledit-input form-control input-sm resizetext" type="text" name="Last"   ></textarea>').appendTo(cell4);
      $('<textarea class="tabledit-input form-control input-sm resizetext" type="text" name="Last"   ></textarea>').appendTo(cell5);
      $('<textarea class="tabledit-input form-control input-sm resizetext" type="text" name="Last"   ></textarea>').appendTo(cell6);
      $('<textarea class="tabledit-input form-control input-sm resizetext" type="text" name="Last"   ></textarea>').appendTo(cell7);
      $('<textarea class="tabledit-input form-control input-sm resizetext" type="text" name="Last"   ></textarea>').appendTo(cell8);
      $('<textarea class="tabledit-input form-control input-sm resizetext" type="text" name="Last"   ></textarea>').appendTo(cell9);
      $('<textarea class="tabledit-input form-control input-sm resizetext" type="text" name="Last"   ></textarea>').appendTo(cell10);
      $('<textarea class="tabledit-input form-control input-sm resizetext" type="text" name="Last"   ></textarea>').appendTo(cell11);
      $('<i class="fa fa-times btnDelete" style="font-size: 20px;color: red"></i>\n').appendTo(cell12);

  }
  function table_cpv(){
      var table = document.getElementById("table_cpv");
      // var numberoftds = document.getElementById("example-1").rows.length;
      var t1=(table.rows.length);
      var row = table.insertRow(t1);
      var cell0 = row.insertCell(0);
      var cell1 = row.insertCell(1);
      var cell2 = row.insertCell(2);
      var cell3 = row.insertCell(3);
      var cell4 = row.insertCell(4);
      var cell5 = row.insertCell(5);
      var cell6 = row.insertCell(6);
      var cell7 = row.insertCell(7);
      var cell8 = row.insertCell(8);
      var cell9 = row.insertCell(9);
      var cell10 = row.insertCell(10);
      var cell11 = row.insertCell(11);
      var cell12 = row.insertCell(12);
      cell0.className='abc';
      cell1.className='abc';
      cell2.className='abc';
      cell3.className='abc';
      cell4.className='abc';
      cell5.className='abc';
      cell6.className='abc';
      cell7.className='abc';
      cell8.className='abc';
      cell9.className='abc';
      cell10.className='abc';
      cell11.className='abc';
      cell12.className='abc';

      $('<input class="tabledit-input form-control input-sm" type="text" name="Last"   >').appendTo(cell0);
      $('<input class="tabledit-input form-control input-sm" type="text" name="Last"   >').appendTo(cell1);
      $('<input class="tabledit-input form-control input-sm" type="text" name="Last"   >').appendTo(cell2);
      $('<input class="tabledit-input form-control input-sm" type="text" name="Last"   >').appendTo(cell3);
      $('<input class="tabledit-input form-control input-sm" type="text" name="Last"   >').appendTo(cell4);
      $('<input class="tabledit-input form-control input-sm" type="text" name="Last"   >').appendTo(cell5);
      $('<input class="tabledit-input form-control input-sm" type="text" name="Last"   >').appendTo(cell6);
      $('<input class="tabledit-input form-control input-sm" type="text" name="Last"   >').appendTo(cell7);
      $('<input class="tabledit-input form-control input-sm" type="text" name="Last"   >').appendTo(cell8);
      $('<input class="tabledit-input form-control input-sm" type="text" name="Last"   >').appendTo(cell9);
      $('<input class="tabledit-input form-control input-sm" type="text" name="Last"   >').appendTo(cell10);
      $('<input class="tabledit-input form-control input-sm" type="text" name="Last"   >').appendTo(cell11);
      $('<i class="fa fa-times btnDelete" style="font-size: 20px;color: red"></i>\n').appendTo(cell12);

  }
  function table_crv(){
      var table = document.getElementById("table_crv");
      // var numberoftds = document.getElementById("example-1").rows.length;
      var t1=(table.rows.length);
      var row = table.insertRow(t1);
      var cell0 = row.insertCell(0);
      var cell1 = row.insertCell(1);
      var cell2 = row.insertCell(2);
      var cell3 = row.insertCell(3);
      var cell4 = row.insertCell(4);
      var cell5 = row.insertCell(5);
      var cell6 = row.insertCell(6);
      var cell7 = row.insertCell(7);
      var cell8 = row.insertCell(8);
      var cell9 = row.insertCell(9);
      var cell10 = row.insertCell(10);
      var cell11 = row.insertCell(11);
      var cell12 = row.insertCell(12);
      cell0.className='abc';
      cell1.className='abc';
      cell2.className='abc';
      cell3.className='abc';
      cell4.className='abc';
      cell5.className='abc';
      cell6.className='abc';
      cell7.className='abc';
      cell8.className='abc';
      cell9.className='abc';
      cell10.className='abc';
      cell11.className='abc';
      cell12.className='abc';

      $('<input class="tabledit-input form-control input-sm" type="text" name="Last"   >').appendTo(cell0);
      $('<input class="tabledit-input form-control input-sm" type="text" name="Last"   >').appendTo(cell1);
      $('<input class="tabledit-input form-control input-sm" type="text" name="Last"   >').appendTo(cell2);
      $('<input class="tabledit-input form-control input-sm" type="text" name="Last"   >').appendTo(cell3);
      $('<input class="tabledit-input form-control input-sm" type="text" name="Last"   >').appendTo(cell4);
      $('<input class="tabledit-input form-control input-sm" type="text" name="Last"   >').appendTo(cell5);
      $('<input class="tabledit-input form-control input-sm" type="text" name="Last"   >').appendTo(cell6);
      $('<input class="tabledit-input form-control input-sm" type="text" name="Last"   >').appendTo(cell7);
      $('<input class="tabledit-input form-control input-sm" type="text" name="Last"   >').appendTo(cell8);
      $('<input class="tabledit-input form-control input-sm" type="text" name="Last"   >').appendTo(cell9);
      $('<input class="tabledit-input form-control input-sm" type="text" name="Last"   >').appendTo(cell10);
      $('<input class="tabledit-input form-control input-sm" type="text" name="Last"   >').appendTo(cell11);
      $('<i class="fa fa-times btnDelete" style="font-size: 20px;color: red"></i>\n').appendTo(cell12);

  }
  function table_jv(){
      var table = document.getElementById("table_jv");
      // var numberoftds = document.getElementById("example-1").rows.length;
      var t1=(table.rows.length);
      var row = table.insertRow(t1);
      var cell0 = row.insertCell(0);
      var cell1 = row.insertCell(1);
      var cell2 = row.insertCell(2);
      var cell3 = row.insertCell(3);
      var cell4 = row.insertCell(4);
      var cell5 = row.insertCell(5);
      var cell6 = row.insertCell(6);
      var cell7 = row.insertCell(7);
      var cell8 = row.insertCell(8);
      var cell9 = row.insertCell(9);
      var cell10 = row.insertCell(10);
      var cell11 = row.insertCell(11);
      var cell12 = row.insertCell(12);
      cell0.className='abc';
      cell1.className='abc';
      cell2.className='abc';
      cell3.className='abc';
      cell4.className='abc';
      cell5.className='abc';
      cell6.className='abc';
      cell7.className='abc';
      cell8.className='abc';
      cell9.className='abc';
      cell10.className='abc';
      cell11.className='abc';
      cell12.className='abc';

      $('<input class="tabledit-input form-control input-sm" type="text" name="Last"   >').appendTo(cell0);
      $('<input class="tabledit-input form-control input-sm" type="text" name="Last"   >').appendTo(cell1);
      $('<input class="tabledit-input form-control input-sm" type="text" name="Last"   >').appendTo(cell2);
      $('<input class="tabledit-input form-control input-sm" type="text" name="Last"   >').appendTo(cell3);
      $('<input class="tabledit-input form-control input-sm" type="text" name="Last"   >').appendTo(cell4);
      $('<input class="tabledit-input form-control input-sm" type="text" name="Last"   >').appendTo(cell5);
      $('<input class="tabledit-input form-control input-sm" type="text" name="Last"   >').appendTo(cell6);
      $('<input class="tabledit-input form-control input-sm" type="text" name="Last"   >').appendTo(cell7);
      $('<input class="tabledit-input form-control input-sm" type="text" name="Last"   >').appendTo(cell8);
      $('<input class="tabledit-input form-control input-sm" type="text" name="Last"   >').appendTo(cell9);
      $('<input class="tabledit-input form-control input-sm" type="text" name="Last"   >').appendTo(cell10);
      $('<input class="tabledit-input form-control input-sm" type="text" name="Last"   >').appendTo(cell11);
      $('<i class="fa fa-times btnDelete" style="font-size: 20px;color: red"></i>\n').appendTo(cell12);

  }
  function add_row_bpv(){
      var table = document.getElementById("table_bpv");
      // var numberoftds = document.getElementById("example-1").rows.length;
      var t1=(table.rows.length);
      var row = table.insertRow(t1);
      var cell0 = row.insertCell(0);
      var cell1 = row.insertCell(1);
      var cell2 = row.insertCell(2);
      var cell3 = row.insertCell(3);
      var cell4 = row.insertCell(4);
      var cell5 = row.insertCell(5);
      var cell6 = row.insertCell(6);
      var cell7 = row.insertCell(7);
      var cell8 = row.insertCell(8);
      var cell9 = row.insertCell(9);
      var cell10 = row.insertCell(10);
      var cell11 = row.insertCell(11);
      var cell12 = row.insertCell(12);
      cell0.className='abc';
      cell1.className='abc';
      cell2.className='abc';
      cell3.className='abc';
      cell4.className='abc';
      cell5.className='abc';
      cell6.className='abc';
      cell7.className='abc';
      cell8.className='abc';
      cell9.className='abc';
      cell10.className='abc';
      cell11.className='abc';
      cell12.className='abc';

      $('<input class="tabledit-input form-control input-sm" type="text" name="Last"   >').appendTo(cell0);
      $('<input class="tabledit-input form-control input-sm" type="text" name="Last"   >').appendTo(cell1);
      $('<input class="tabledit-input form-control input-sm" type="text" name="Last"   >').appendTo(cell2);
      $('<input class="tabledit-input form-control input-sm" type="text" name="Last"   >').appendTo(cell3);
      $('<input class="tabledit-input form-control input-sm" type="text" name="Last"   >').appendTo(cell4);
      $('<input class="tabledit-input form-control input-sm" type="text" name="Last"   >').appendTo(cell5);
      $('<input class="tabledit-input form-control input-sm" type="text" name="Last"   >').appendTo(cell6);
      $('<input class="tabledit-input form-control input-sm" type="text" name="Last"   >').appendTo(cell7);
      $('<input class="tabledit-input form-control input-sm" type="text" name="Last"   >').appendTo(cell8);
      $('<input class="tabledit-input form-control input-sm" type="text" name="Last"   >').appendTo(cell9);
      $('<input class="tabledit-input form-control input-sm" type="text" name="Last"   >').appendTo(cell10);
      $('<input class="tabledit-input form-control input-sm" type="text" name="Last"   >').appendTo(cell11);
      $('<i class="fa fa-times btnDelete" style="font-size: 20px;color: red"></i>\n').appendTo(cell12);

  }
 function add_row_payment(){
     var table = document.getElementById("example-1");
     // var numberoftds = document.getElementById("example-1").rows.length;
     var t1=(table.rows.length - 6);
     var row = table.insertRow(t1);
     var cell0 = row.insertCell(0);
     var cell1 = row.insertCell(1);
     var cell2 = row.insertCell(2);
     var cell3 = row.insertCell(3);
     var cell4 = row.insertCell(4);
     var cell5 = row.insertCell(5);
     var cell6 = row.insertCell(6);
     var cell7 = row.insertCell(7);
     var cell8 = row.insertCell(8);
     var cell9 = row.insertCell(9);
     var cell10 = row.insertCell(10);
     var cell11 = row.insertCell(11);
     var cell12 = row.insertCell(12);
     cell0.className='abc';
     cell1.className='abc';
     cell2.className='abc';
     cell3.className='abc';
     cell4.className='abc';
     cell5.className='abc';
     cell6.className='abc';
     cell7.className='abc';
     cell8.className='abc';
     cell9.className='abc';
     cell10.className='abc';
     cell11.className='abc';
     cell12.className='abc';

     $('<textarea cols="30" rows="1" class="tabledit-input form-control input-sm resizetext" type="text" id="inv_no" name="inv_no[]"  ></textarea>\n').appendTo(cell0);
     $('<textarea cols="30" rows="1" class="tabledit-input form-control input-sm resizetext" type="text" id="description" name="description[]"  ></textarea>').appendTo(cell1);
     $(' <select class="tabledit-input form-control input-sm" id="br_dep_code" name="br_dep_code[]">\n' +
         '                                                                        <option value="001">001</option>\n' +
         '                                                                        <option value="002">002</option>\n' +
         '                                                                        <option value="003">003</option>\n' +
         '                                                                    </select>').appendTo(cell2);
     $(' <select class="tabledit-input form-control input-sm" id="budget_code" name="budget_code[]">\n' +
         '                                                                        <option value="001">001</option>\n' +
         '                                                                        <option value="002">002</option>\n' +
         '                                                                        <option value="003">003</option>\n' +
         '                                                                    </select>').appendTo(cell3);
     $(' <select class="tabledit-input form-control input-sm" id="alternative_budget_code" name="alternative_budget_code[]">\n' +
         '                                                                        <option value="001">001</option>\n' +
         '                                                                        <option value="002">002</option>\n' +
         '                                                                        <option value="003">003</option>\n' +
         '                                                                    </select>').appendTo(cell4);
     $('<textarea cols="30" rows="1" class="tabledit-input form-control input-sm resizetext" type="text" id="unit" name="unit[]"  ></textarea>').appendTo(cell5);
     $(' <textarea cols="30" rows="1" class="tabledit-input form-control input-sm resizetext" type="text" id="qty" name="qty[]"  ></textarea>').appendTo(cell6);
     $('<textarea cols="30" rows="1" class="tabledit-input form-control input-sm resizetext" type="text" id="unit_price" name="unit_price[]"  ></textarea>').appendTo(cell7);
     $('<textarea cols="30" rows="1" class="tabledit-input form-control input-sm resizetext" type="text" id="total" name="total[]"  ></textarea>').appendTo(cell8);
     $('<textarea cols="30" rows="1" class="tabledit-input form-control input-sm resizetext" type="text" id="ytd_expense" name="ytd_expense[]"  ></textarea>').appendTo(cell9);
     $(' <textarea cols="30" rows="1" class="tabledit-input form-control input-sm resizetext" type="text" id="total_budget" name="total_budget[]" ></textarea>').appendTo(cell10);
     $('<textarea cols="30" rows="1" class="tabledit-input form-control input-sm resizetext" type="text" id="within_budget" name="within_budget[]"  ></textarea>').appendTo(cell11);
     $('<i class="fa fa-times btnDelete" style="font-size: 20px;color: red"></i>\n').appendTo(cell12);

 }
function add_row()
{
    var table = document.getElementById("example-1");
    // var numberoftds = document.getElementById("example-1").rows.length;
    var t1=(table.rows.length - 1);
    var row = table.insertRow(t1);
    var cell0 = row.insertCell(0);
    var cell1 = row.insertCell(1);
    var cell2 = row.insertCell(2);
     var cell3 = row.insertCell(3);
     var cell4 = row.insertCell(4);
     var cell5 = row.insertCell(5);
     var cell6 = row.insertCell(6);
     var cell7 = row.insertCell(7);
     var cell8 = row.insertCell(8);
     var cell9 = row.insertCell(9);
     var cell10 = row.insertCell(10);
cell0.className='abc';
cell1.className='abc';
cell2.className='abc';
cell3.className='abc';
cell4.className='abc';
cell5.className='abc';
cell6.className='abc';
cell7.className='abc';
cell8.className='abc';
cell9.className='abc';
cell10.className='abc';

     $('<textarea cols="30" rows="1" class="tabledit-input form-control input-sm resizetext" type="text" id="description" name="description[]"  ></textarea>').appendTo(cell0);
     $(' <select class="tabledit-input form-control input-sm" id="br_dep_code" name="br_dep_code[]">\n' +
         '                                                                        <option value="001">001</option>\n' +
         '                                                                        <option value="002">002</option>\n' +
         '                                                                        <option value="003">003</option>\n' +
         '                                                                    </select>').appendTo(cell1);
    $(' <select class="tabledit-input form-control input-sm" id="budget_code" name="budget_code[]">\n' +
        '                                                                        <option value="001">001</option>\n' +
        
        
        '                                                                    </select>').appendTo(cell2);
    $(' <select class="tabledit-input form-control input-sm" id="alternative_budget_code" name="alternative_budget_code[]">\n' +
        '                                                                        <option value="001">001</option>\n' +
        '                                                                        <option value="002">002</option>\n' +
        '                                                                        <option value="003">003</option>\n' +
        '                                                                    </select>').appendTo(cell3);
    $('<textarea cols="30" rows="1" class="tabledit-input form-control input-sm resizetext" id="unit" name="unit[]"  ></textarea>').appendTo(cell4);
    $('<textarea cols="30" rows="1" class="tabledit-input form-control input-sm resizetext" type="text" id="qty" name="qty[]"  ></textarea>').appendTo(cell5);
    $('<textarea cols="30" rows="1" class="tabledit-input form-control input-sm resizetext" type="text" id="unit_price" name="unit_price[]"  ></textarea>').appendTo(cell6);
    $('<textarea cols="30" rows="1" class="tabledit-input form-control input-sm resizetext" type="text" id="total_estimate" name="total_estimate[]"  ></textarea>').appendTo(cell7);
    $('<textarea cols="30" rows="1" class="tabledit-input form-control input-sm resizetext" type="text" id="delivery_date" name="delivery_date[]"  ></textarea>').appendTo(cell8);
    $('<textarea cols="30" rows="1" class="tabledit-input form-control input-sm resizetext" type="text" id="within_budget" name="within_budget[]"  ></textarea>').appendTo(cell9);
    $('<i class="fa fa-times btnDelete" style="font-size: 20px;color: red"></i>\n').appendTo(cell10);

};

