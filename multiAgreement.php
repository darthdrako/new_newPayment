<?
session_start();
include("../../libs/db.class.php");
if($_REQUEST['calendar']){
    $calendar = $_REQUEST['calendar'];
}else{
    $db = NEW DB();
    $calendarId = $db->doSql("SELECT calendar FROM warranty WHERE id=".$_REQUEST['warrantyId']);
    $calendar = $calendarId['calendar'];
    $warranty = "&garantia=true_pay";
}
$route = $_REQUEST['route'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Convenios</title>
    <link rel="stylesheet" href="../../tools/jqwidgets/jqwidgets/styles/jqx.base.css" type="text/css" />
    <link rel="stylesheet" href="../../tools/jqwidgets/jqwidgets/styles/jqx.metro.css" type="text/css" />
    <link rel="stylesheet" href="../../tools/jqwidgets/jqwidgets/styles/jqx.darkblue.css" type="text/css" />
    <script type="text/javascript" src="../../js/jquery-1.8.1.min.js"></script>
    <script type="text/javascript" src="../../js/jquery-ui.js"></script>
    <link rel="stylesheet" href="../../tools/jquery-window/css/jquery.window.css" type="text/css" media="screen">
    <script type="text/javascript" src="../../tools/jqwidgets/jqwidgets/jqx-all.js"></script>
    <script type="text/javascript" src="../../tools/jquery-window/jquery.window.js"></script>
    <script type="text/javascript" src="../../js/es_chile.js"></script>
    <style>
        html, body{width: 99%; height: 98%;}
        body {
            font: 14px "Lucida Grande","Lucida Sans Unicode",Helvetica,Arial,Verdana,sans-serif;
            color: #444; 
            -webkit-font-smoothing: antialiased; /* Fix for webkit rendering */
            -webkit-text-size-adjust: none;
        }
        .center .ui-jqgrid { margin-left: 50% auto; margin-right: 50% auto;}
        #buttons{
            margin-top: 20px;
            text-align: center;
        }
    </style>
    <script type="text/javascript">
    function close_pay() {
        window.parent.close_pay();
    }
        var totalValue = 0;
        var theme = 'darkblue';

        $(document).ready(function () {
            var calendar = <?php echo $calendar; ?>;
            var route = '<?php echo $route; ?>';

            // prepare the data
            var imagerenderer = function (row, datafield, value) {
                var rowid = $("#jqxGrid").jqxGrid('getrowid', row);
                return '<img onclick="view('+rowid+')" style="cursor: pointer; margin-left: 30%; margin-bottom: 5px; margin-top: 1%;"  width="auto" src="../../../images/history_view.png"/>';
            }
            var source =
            {
                datatype: "json",
                datafields: [
                    /*{ name: 'Nombre' },
                    { name: 'Doctor' }*/
                ],
                id: 'id',
                url: 'getHyperPayment.php?calendar='+calendar,
                pagesize: 10,
                pager: function (pagenum, pagesize, oldpagenum) {
                    // callback called when a page or page size is changed.
                },
                root: 'data'

            };

            // initialize jqxGrid
            var initi=1;
            $("#jqxGrid").jqxGrid(
            {
                width: '99%',
                source: source,
                theme: theme,
                filterable: true,
                showfilterrow: false,
                sortable: true,
                //pageable: true,
                columnsresize: true,
                columnsreorder: true,
                editable: true,
                showstatusbar: true,
                showaggregates: true,
                editmode: 'selectedcell',
                //selectionmode: 'checkbox',
                pagesizeoptions: ['10', '20', '30'],
                localization: getLocalization(),
                altrows: true,
                columns: [
                    { text: 'Id', datafield: 'id', width: '10%', editable: false, },
                    { text: 'Id Examen', datafield: 'id_exam', width: '10%', editable: false, },
                    { text: 'Nombre', datafield: 'name', width: '40%', editable: false },
                    { text: 'Convenio', columntype: 'dropdownlist', datafield: 'Convenio',editable: true, width: '20%', 
                        initeditor: function (row, cellvalue, editor) {
                            this.createeditor(row, cellvalue, editor);
                        },

                        createeditor: function (row, cellvalue, editor) {
                            var data = $('#jqxGrid').jqxGrid('getrowdata', row);
                            var urlAgreement = 'getAgreement2.php?exam='+data.id_exam+"&row="+row;
                            // prepare the data
                            var dropDownListSource =
                            {
                                datatype: "json",
                                datafields: [
                                    { name: 'name' },
                                    { name: 'Convenio'},
                                    { name: 'price'},
                                    { name: 'agreement'}
                                ],
                                id: 'id',
                                url: urlAgreement
                            };
                            if (initi>0){
                                initi--;
                                var dropdownListAdapter = new $.jqx.dataAdapter(dropDownListSource, { autoBind: false, async: false });
                            }else{
                                var dropdownListAdapter = new $.jqx.dataAdapter(dropDownListSource, { autoBind: false, async: true });
                            }
                            editor.jqxDropDownList({ source: dropdownListAdapter, displayMember: 'name', valueMember: 'price'});
                            editor[0].title = ""+row+"";

                        },      
                    },

                    { text: 'Valor', datafield: 'Valor', width: '30%', editable: false, aggregates: ['sum'],
                        aggregatesrenderer: function (aggregates, column, element) {
                            totalValue = aggregates['sum'];
                            var renderstring = "<div class='jqx-widget-content jqx-widget-content-metro' style='float: left; width: 100%; height: 100%;'>";
                            $.each(aggregates, function (key, value) {
                              renderstring += '<div style="position: relative; margin: 6px; text-align: left; overflow: hidden;">Total: ' + value + '</div>';
                            });
                            renderstring += "</div>";
                            return renderstring;
                        }
                    }, 

                    { text: 'Agreement', datafield: 'agreement', width: '1%', editable: false, hidden: true}
                    
                ]
            });
            //Defino Menu Contextual
            /*var contextMenu = $("#Menu").jqxMenu({ width: 200, height: 90, autoOpenPopup: false, mode: 'popup' });
            $("#jqxGrid").on('contextmenu', function (e) {
                return false;
            });*/

            $("#jqxGrid").change(function (event) {
                var args = event.args;

                if (initi>0){
                    initi--;
                }else{
                    if(args.index != -1){
                        var data = event.args.item.originalItem.price.split('-');
                        var price = data[0];
                        index2 = data[1];
                        var id = data[2];

                        setTimeout(function(){$("#jqxGrid").jqxGrid('setcellvalue', event.target.title, "Valor", price)},500);
                        setTimeout(function(){$("#jqxGrid").jqxGrid('setcellvalue', event.target.title, "agreement", id);},500);
                    }
                }
            });
       
            $("#jqxButton").jqxButton({ width: '150', theme: theme });
            $("#jqxButton").on('click', function () {

                var validation = true;
                var arreglo = $('#jqxGrid').jqxGrid('getrows');
                for(i=0; i<arreglo.length;i++){
                    if(arreglo[i].Convenio=='Seleccione Convenio...' || arreglo[i].Convenio==''){
                        validation=false;
                        i=arreglo.length;
                    }
                }
                if (validation==false){
                    alert("Faltan convenios a seleccionar");
                }else{
                    //alert("Convenios Seleccionados Satisfactoriamente");
                    //var valor = eval('(' + arreglo + ')');
                    var warranty = '<? echo $warranty;?>';
                    if(route) var url = "../payment_new/index.php?calendar="+calendar+"&route=1"+"&total="+totalValue+"&agreement="+JSON.stringify(arreglo)+warranty;
                    else url = "../payment_new/index.php?calendar="+calendar+"&total="+totalValue+"&agreement="+JSON.stringify(arreglo)+warranty;
                    window.location.assign(url);
                   // http://200.68.17.197/newbioris/inc/main.php?module=payment&calendarId=314724&agreement=2496
                    //window.href='';
                }
            });

            //Click en opcion de menu
            /*$("#Menu").on('itemclick', function (event) {
                var args = event.args;
                var rowindex = $("#jqxGrid").jqxGrid('getselectedrowindex');
                if ($.trim($(args).text()) == "Borrar"){
                    var rowid = $("#jqxGrid").jqxGrid('getrowid', rowindex);
                    var dataRecord = $("#jqxGrid").jqxGrid('getrowdata', rowindex);
                    $.post('../../connectors/deleteReport.php', {id: dataRecord.id}, function(data) {
                        $('#jqxGrid').jqxGrid('deleterow', dataRecord.id);
                    });
                    //$("#jqxGrid").jqxGrid('deleterow', rowid);   ESTO ES PARA BORRAR UN REGISTRO DE LA GRILLA
                }else if ($.trim($(args).text()) == "Ver"){
                    var rowid = $("#jqxGrid").jqxGrid('getrowid', rowindex);
                }else if ($.trim($(args).text()) == "Editar"){
                    var rowid = $("#jqxGrid").jqxGrid('getrowid', rowindex);
                    var title = "Editor de Informe";
                    var url = url =  "reportUpdate.php?update="+ rowid;
                    var msg = "Informes";
                    window.location.assign(url);
                }
            });*/
            $('#jqxGrid').jqxGrid({ autoheight: true});
            //$('#jqxGrid').jqxGrid('hidecolumn', 'Agreement');

        });
    </script>
</head>
<body class='default'>
<div id="window-container">
    <div id='jqxWidget'>
        <div id="jqxGrid"></div>
        <!--<div id='Menu'>
            <ul>
                <li>Editar</li>
                <li>Borrar</li>
            </ul>
        </div>-->
    </div>
</div>
    <div id="buttons">
        <input type="button" value="Pagar" id="jqxButton" />
    </div>
</body>
</html>