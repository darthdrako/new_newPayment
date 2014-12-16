<?php

//$calendar = $_REQUEST['calendar'];
$calendar = 217141;
$totalAgreements = 22000;
?>

<html>
	<head>
		<meta charset="utf-8">
		<script src="js/jquery.min.js"></script>
		<script src="js/bootstrap/js/bootstrap.min.js"></script>
		<link href="js/bootstrap/css/bootstrap.min.css" rel="stylesheet">
		<script src="js/datepicker/js/bootstrap-datepicker.js"></script>
		<script type="text/javascript" src="js/datepicker/js/locales/bootstrap-datepicker.es.js" charset="UTF-8"></script>
		<link href="js/datepicker/css/datepicker.css" rel="stylesheet">
		<link href="js/font-awesome-4.2.0/css/font-awesome.min.css" rel="stylesheet">
		<script>
			var countCash = 0, countBonus = 0,countCredit = 0,countDebit = 0,countCheque = 0,countWarranty = 0;
			var paymentArray = [];
			var calendar = '<?php echo $calendar; ?>';

			$(document).ready(function() {
				
				detailCalendar(calendar);
				$('#paymentSelect a').click(function (e) {
					resetClass();
					e.preventDefault();
					console.log($(this));
					$(this).tab('show')
				});
				
				$(".onlyNumeric").keypress(function(e) {
				 	validateNumber(e);
				});
				$("#myTabContent :input").keypress(function(event) {
					$(this).parent().removeClass('has-error has-feedback');
				});

				//Seteo de los calendarios
				$('.datepicker').datepicker({
				    format: 'yyyy/mm/dd',
				    language: 'es',
				    todayHighlight: true,
				    todayBtn: true
				});
				var today = new Date();
				var todayDate = today.getFullYear()+'/'+(today.getMonth()+1)+'/'+today.getDate();
				$(".datepicker").datepicker('update', todayDate);
				$(".datepicker").keypress(function(e) {
				 	return false;
				});

			});
			function getObjects(obj, key, val) {
				var objects = [];
				for (var i in obj) {
					if (!obj.hasOwnProperty(i)) continue;
					if (typeof obj[i] == 'object') {
						objects = objects.concat(getObjects(obj[i], key, val));
					} else if (i == key && obj[key] == val) {
						objects.push(obj);
					}
				}
				return objects;
			}
			function detailCalendar (calendar) {
				var html = '';
				var totalAgreements = '<?php echo $totalAgreements; ?>';
				var popoverTable = '';

				$.post('phps/detailCalendar.php', {calendar: calendar}, function(data, textStatus, xhr) {
					for (var i = 0; i < data.exams.length; i++) {
						if( i == 0 ) popoverTable += ''+(i+1)+'.- '+data.exams[i].name+'';
						else popoverTable += '<br>'+(i+1)+'.- '+data.exams[i].name+'';
					};
					html += '<td>'+data.paciente+'</td>';
					html += '<td>'+data.date+'</td>';
					html += '<td>'+data.hour+'</td>';
					html += '<td><button type="button" class="btn btn-md btn-danger" data-placement="auto top" data-trigger="hover" data-toggle="popover" data-html="true" title="Examenes y Convenios" data-content="'+popoverTable+'">Examenes/Convenios</button></td>';
					html += '<td id="toPay">'+totalAgreements+'</td>';
					$('#detail').html(html);
					
					$('#abonoTotal').html(data.pay);
					$('[data-toggle="popover"]').popover()
				});
			}
			function resetClass() {
				$('#cashTicketDiv').removeClass('has-error has-feedback');
				$('#cashValueDiv').removeClass('has-error has-feedback');
				$('#bonusTicketDiv').removeClass('has-error has-feedback');
				$('#bonusValueDiv').removeClass('has-error has-feedback');
				$('#bonusValueCoDiv').removeClass('has-error has-feedback');
				$('#creditTicketDiv').removeClass('has-error has-feedback');
				$('#creditValueDiv').removeClass('has-error has-feedback');
				$('#creditUserDiv').removeClass('has-error has-feedback');
				$('#chequeTicketDiv').removeClass('has-error has-feedback');
				$('#chequeValueDiv').removeClass('has-error has-feedback');
				$('#chequeUserDiv').removeClass('has-error has-feedback');
				$('#creditTicketDiv').removeClass('has-error has-feedback');
				$('#creditValueDiv').removeClass('has-error has-feedback');
				$('#creditUserDiv').removeClass('has-error has-feedback');
				$('#debitTicketDiv').removeClass('has-error has-feedback');
				$('#debitValueDiv').removeClass('has-error has-feedback');
				$('#debitUserDiv').removeClass('has-error has-feedback');
			}
			function insertPayment(type){
				var detail='';
				var closeButton = '<button style="z-index:9999;" type="button" class="close" onclick="deletePayment(this,\''+type+'\')" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>';
				if(type=="cash"){
					if($("#cashValue").val()!='' && $("#cashTicket").val()!=''){
						detail='<div style="z-index:998;" id="cash-'+countCash+'" class="alert alert-success alert-dismissible fade in" role="alert">'+closeButton+'<strong>Efectivo</strong><br/> Boleta: '+$("#cashTicket").val()+', Valor: '+$("#cashValue").val()+'</div>';
						paymentArray.push({type:"cash", id: countCash, alert: "cash-"+countCash, ticket: $("#cashTicket").val(), value: $("#cashValue").val()});
						countCash++;
						calculate();
						$("#cashValue").val(''); 
						$("#cashTicket").val(''); 
						$("#cashValue").prop("disabled",true);
						$("#cashTicket").prop("disabled",true);
						$("#cashButton").prop("disabled",true);

					}else{
						/*$.each($("#cash :input"), function(index, val) {
							//has-error has-feedback
							 console.log(val);
						});*/
						if($("#cashTicket").val()=='') $('#cashTicketDiv').addClass('has-error has-feedback');
						if($("#cashValue").val()=='') $('#cashValueDiv').addClass('has-error has-feedback');
						//$("#modalEmpty").modal('show');
					}

				}else if(type=="bonus"){
					if($("#bonusTicket").val()!='' && $("#bonusValue").val()!='' && $("#bonusValueCo").val()!=''){
						detail='<div id="bonus-'+countBonus+'" class="alert-payment alert alert-info alert-dismissible fade in" role="alert">'+closeButton+'<strong>Bono</strong><br/> Nº '+$("#bonusTicket").val()+', Valor Bono: '+$("#bonusValue").val()+', Copago: '+$("#bonusValueCo").val()+'</div>';
						paymentArray.push({type:"bonus", id: countBonus, alert: "bonus-"+countBonus, ticket: $("#bonusTicket").val(), value: $("#bonusValue").val(), covalue: $("#bonusValueCo").val()});
						countBonus++;
						calculate();
						$("#bonusTicket").val(''); 
						$("#bonusValue").val(''); 
						$("#bonusValueCo").val(''); 
					}else{
						if($("#bonusTicket").val()=='') $('#bonusTicketDiv').addClass('has-error has-feedback');
						if($("#bonusValue").val()=='') $('#bonusValueDiv').addClass('has-error has-feedback');
						if($("#bonusValueCo").val()=='') $('#bonusValueCoDiv').addClass('has-error has-feedback');
						//$("#modalEmpty").modal('show');
					}

				}else if(type=="credit"){
					if($("#creditTicket").val()!='' && $("#creditValue").val()!='' && $("#creditUser").val()!=''){
						detail='<div id="credit-'+countCredit+'" class="alert-payment alert alert-info alert-dismissible fade in" role="alert">'+closeButton+'<strong>Crédito</strong><br/> Emisor: '+$("#creditUser").val()+', Nº de Transacción: '+$("#creditTicket").val()+', Valor: '+$("#creditValue").val()+'</div>';
						paymentArray.push({type:"credit", id: countCredit, alert: "credit-"+countCredit, ticket: $("#creditTicket").val(), value: $("#creditValue").val(), user: $("#creditUser").val()});
						countCredit++;
						calculate();
						$("#creditTicket").val(''); 
						$("#creditValue").val(''); 
						$("#creditUser").val(''); 
					}else{
						if($("#creditTicket").val()=='') $('#creditTicketDiv').addClass('has-error has-feedback');
						if($("#creditValue").val()=='') $('#creditValueDiv').addClass('has-error has-feedback');
						if($("#creditUser").val()=='') $('#creditUserDiv').addClass('has-error has-feedback');
					}

				}else if(type=="debit"){
					if($("#debitTicket").val()!='' && $("#debitValue").val()!='' && $("#debitUser").val()!=''){
						detail='<div id="debit-'+countDebit+'" class="alert-payment alert alert-warning alert-dismissible fade in" role="alert">'+closeButton+'<strong>Débito</strong><br/> Emisor: '+$("#debitUser").val()+', Nº de Transacción: '+$("#debitTicket").val()+', Valor: '+$("#debitValue").val()+'</div>';
						paymentArray.push({type:"debit", id: countDebit, alert: "debit-"+countDebit, ticket: $("#debitTicket").val(), value: $("#debitValue").val(), user: $("#debitUser").val()});
						countDebit++;
						calculate();
						$("#debitTicket").val(''); 
						$("#debitValue").val(''); 
						$("#debitUser").val(''); 
					}else{
						if($("#debitTicket").val()=='') $('#debitTicketDiv').addClass('has-error has-feedback');
						if($("#debitValue").val()=='') $('#debitValueDiv').addClass('has-error has-feedback');
						if($("#debitUser").val()=='') $('#debitUserDiv').addClass('has-error has-feedback');
					}

				}else if(type=="cheque"){
					if($("#chequeTicket").val()!='' && $("#chequeValue").val()!='' && $("#chequeUser").val()!=''){
						detail='<div id="cheque-'+countCheque+'" class="alert-payment alert alert-warning alert-dismissible fade in" role="alert">'+closeButton+'<strong>Cheque</strong><br/> Emisor: '+$("#chequeUser").val()+', Nº de Transacción: '+$("#chequeTicket").val()+', Valor: '+$("#chequeValue").val()+'</div>';
						paymentArray.push({type:"cheque", id: countCheque, alert: "cheque-"+countCheque, ticket: $("#chequeTicket").val(), value: $("#chequeValue").val(), user: $("#chequeUser").val()});
						countCheque++;
						calculate();
						$("#chequeTicket").val(''); 
						$("#chequeValue").val('');  
						$("#chequeUser").val(''); 
					}else{
						if($("#chequeTicket").val()=='') $('#chequeTicketDiv').addClass('has-error has-feedback');
						if($("#chequeValue").val()=='') $('#chequeValueDiv').addClass('has-error has-feedback');
						if($("#chequeUser").val()=='') $('#chequeUserDiv').addClass('has-error has-feedback');
					}

				}else if(type=="warranty"){
					if($("#warrantyObservation").val()!='' && $("#warrantyValue").val()!='' && $("#warrantyDate").val()!=''){
						detail='<div id="warranty-'+countWarranty+'" class="alert-payment alert alert-danger alert-dismissible fade in " role="alert">'+closeButton+'<strong>Garantía</strong><br/> Observaciones: '+$("#warrantyObservation").val()+', Valor: '+$("#warrantyValue").val()+', Fecha: '+$("#warrantyDate").val()+'</div>';
						paymentArray.push({type:"warranty", id: countWarranty, alert: "warranty-"+countWarranty, observation: $("#warrantyObservation").val(), value: $("#warrantyValue").val(), date: $("#warrantyDate").val()});
						countWarranty++;
						calculate();
						$("#warrantyObservation").val(''); 
						$("#warrantyValue").val(''); 
						$("#warrantyDate").val(''); 
					}else{
						$("#modalEmpty").modal('show');
					}
				}

				$("#paymentDetail").append(detail);
				$(function() {
					$(".alert").click(function(event) {
						var id = $(this).get(0).id;
						var paymentDetail = getObjects(paymentArray, 'alert', id);
						if(paymentDetail[0] !== undefined){
							var type = paymentDetail[0].type;
							switch (type) {
								case "cash":
									$('#cash-tab a').tab('show');
									$("#cashValue").val(paymentDetail[0].value);
									$("#cashTicket").val(paymentDetail[0].ticket);
									$("#cashValue").prop("disabled",false);
									$("#cashTicket").prop("disabled",false);
									$("#cashButton").prop("disabled",false);
									break;
								case "bonus":
									$('#bonus-tab a').tab('show');
									$("#bonusTicket").val(paymentDetail[0].ticket);
									$("#bonusValue").val(paymentDetail[0].value);
									$("#bonusValueCo").val(paymentDetail[0].covalue);
									break;
								case "debit":
									$('#debit-tab a').tab('show');
									$("#debitTicket").val(paymentDetail[0].ticket); 
									$("#debitValue").val(paymentDetail[0].value); 
									$("#debitUser").val(paymentDetail[0].user);
									break;
								case "credit":
									$('#credit-tab a').tab('show');
									$("#creditTicket").val(paymentDetail[0].ticket); 
									$("#creditValue").val(paymentDetail[0].value); 
									$("#creditUser").val(paymentDetail[0].user);
									break;
								case "warranty":
									$('#warranty-tab a').tab('show');
									$("#warrantyObservation").val(paymentDetail[0].observation); 
									$("#warrantyValue").val(paymentDetail[0].value); 
									$("#warrantyDate").val(paymentDetail[0].date);
									break;
								case "cheque":
									$('#cheque-tab a').tab('show');
									$("#chequeTicket").val(paymentDetail[0].ticket); 
									$("#chequeValue").val(paymentDetail[0].value);  
									$("#chequeUser").val(paymentDetail[0].user);
									break;
							}
							updatePayment(this, "cash");
						}
					});
				});
			}

			function deletePayment(payment,type){
				if(type=='cash'){
					$("#cashValue").removeAttr('disabled');
					$("#cashTicket").removeAttr('disabled');
					$("#cashButton").removeAttr('disabled');
				}
				var paymentId = $(payment).parent().attr('id').split('-');
				for(i=0;i<paymentArray.length;i++){
					if(paymentArray[i].type==paymentId[0] && paymentArray[i].id==paymentId[1]){
						paymentArray.splice(i,1);
						break;
					}
				}
				calculate();
			}

			function updatePayment(payment,type){
				if(type=='cash'){
					$("#cashValue").removeAttr('disabled');
					$("#cashTicket").removeAttr('disabled');
					$("#cashButton").removeAttr('disabled');
				}
				var paymentId = payment.id.split('-');
				for(i=0;i<paymentArray.length;i++){
					if(paymentArray[i].type==paymentId[0] && paymentArray[i].id==paymentId[1]){
						paymentArray.splice(i,1);
						break;
					}
				}
				$("#"+payment.id).remove();
				calculate();
			}

			function calculate(){
				if(paymentArray.length>0){
					var cashTotal=0, bonusTotal=0, bonusCoTotal=0, creditTotal=0, debitTotal=0, chequeTotal=0, warrantyTotal=0;
					for(i=0;i<paymentArray.length;i++){
						if(paymentArray[i].type=='cash') cashTotal = cashTotal + parseInt(paymentArray[i]['value']);
						else if(paymentArray[i].type=='bonus'){
							bonusTotal = bonusTotal + parseInt(paymentArray[i]['value']);
							bonusCoTotal = bonusCoTotal + parseInt(paymentArray[i]['covalue']); 
						}else if(paymentArray[i].type=='credit') creditTotal = creditTotal + parseInt(paymentArray[i]['value']);
						else if(paymentArray[i].type=='debit') debitTotal = debitTotal + parseInt(paymentArray[i]['value']);
						else if(paymentArray[i].type=='cheque') chequeTotal = chequeTotal + parseInt(paymentArray[i]['value']);
						else if(paymentArray[i].type=='warranty') warrantyTotal = warrantyTotal + parseInt(paymentArray[i]['value']);
					}

					$("#cashTotal").html(cashTotal);
					$("#bonusTotal").html(bonusTotal);
					$("#bonusCoTotal").html(bonusCoTotal);
					$("#creditTotal").html(creditTotal);
					$("#debitTotal").html(debitTotal);
					$("#chequeTotal").html(chequeTotal);
					$("#warrantyTotal").html(warrantyTotal);
					$("#total").html(cashTotal+bonusTotal+bonusCoTotal+creditTotal+debitTotal+chequeTotal+warrantyTotal);
				}else{
					$("#cashTotal").html(0);
					$("#bonusTotal").html(0);
					$("#bonusCoTotal").html(0);
					$("#creditTotal").html(0);
					$("#debitTotal").html(0);
					$("#chequeTotal").html(0);
					$("#warrantyTotal").html(0);
					$("#total").html(0);
				}

			}

			function savePayment(){//EN DESARROLLO
				var ticketValue = 0;
				var ticket = '';
						
				if($("#toPay").html()==$("#total").html()){
					
					if(paymentArray.length>0){
						for(i=0;i<paymentArray.length;i++){
							if(paymentArray[i].type=='cash'){
								ticket=paymentArray[i].ticket;
								ticketValue=paymentArray[i].value;
							}
						}
						$.post('phps/savePayment.php', {calendar: calendar, cash: ticketValue, code_ticket: ticket}, function(data, textStatus, xhr) {
							console.log(data);
						});

						for(j=0;j<paymentArray.length;j++){
							if(paymentArray[j].type!='cash'){
								paymentArray[j].calendar=calendar;
								$.post('phps/saveAnother.php', paymentArray[j] /*agregar calendar*/, function(data, textStatus, xhr) {
									console.log(data);
								});
							}
						}
					}

					alert("Pagando...");
				}else{
					alert("Epa! Paga poh', gilculeco");
				}
			}


			function validateNumber(evt) {
				var theEvent = evt || window.event;
				var key = theEvent.keyCode || theEvent.which;
				key = String.fromCharCode( key );
				var regex = /[0-9]|\./;
				if( !regex.test(key) ) {
					theEvent.returnValue = false;
					if(theEvent.preventDefault) theEvent.preventDefault();
				}
			}
		</script>
	</head>
	<body>
		<div class="container">
			<div class="row">
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
					<div class="panel panel-info">
							<div class="panel-heading">
								<h3 class="panel-title">Datos de la Atención</h3>
							</div>
							<table class="table table-bordered">
								<thead>
									<tr>
										<th>Paciente</th>
										<th>Fecha</th>
										<th>Hora</th>
										<th>Examen/Convenio</th>
										<th>Total a Pagar</th>
									</tr>
								</thead>
								<tbody>
									<tr id="detail">
									</tr>
								</tbody>
							</table>
					</div>
				</div>
				<div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
					<div class="panel panel-primary">
						<div class="panel-heading ">Información de Pago</div>
						<div class="panel-body">
							<ul id="paymentSelect" class="nav nav-tabs nav-justified">
								<li id="cash-tab" role="presentation" class="active tabs"><a href="#cash" role="tab" data-toggle="tab" aria-controls="cash" aria-expanded="true"><span class="fa fa-money"></span>&nbsp;Efectivo</a></li>
								<li id="bonus-tab" role="presentation" class="tabs"><a href="#bonus"><span class="fa fa-list-alt"></span>&nbsp;Bono</a></li>
								<li id="credit-tab" role="presentation" class="tabs"><a href="#credit"><span class="fa fa-credit-card"></span>&nbsp;Crédito</a></li>
								<li id="debit-tab" role="presentation" class="tabs"><a href="#debit"><span class="glyphicon glyphicon-credit-card"></span>&nbsp;Débito</a></li>
								<li id="cheque-tab" role="presentation" class="tabs"><a href="#cheque"><span class="fa fa-ticket"></span>&nbsp;Cheque</a></li>
								<li id="warranty-tab" role="presentation" class="tabs"><a href="#warranty"><span class="glyphicon glyphicon-transfer"></span>&nbsp;Garantía</a></li>
							</ul>
							
							<!--EFECTIVO-->
							<div id="myTabContent" class="tab-content">
								<div role="tabpanel" class="tab-pane fade in active" id="cash" aria-labelledby="cash-tab">
									<br/>		
									<div class="row" >
										<div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
											<div id ="cashTicketDiv" class="input-group">
												<span class="input-group-addon">Nº Boleta</span>
												<input id="cashTicket" type="text" class="form-control">
											</div>
										</div>
										<div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
											<div id ="cashValueDiv" class="input-group input-group-primary">
												<span class="input-group-addon">Monto $</span>
												<input id="cashValue" type="text" class="form-control onlyNumeric">
											</div>
										</div>
										<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
											<button id="cashButton" type="button" class="btn btn-success" onclick="insertPayment('cash');">Aceptar</button>
										</div>
									</div>

					        	</div>
					        	<!--BONO-->
					        	<div role="tabpanel" class="tab-pane fade in" id="bonus" aria-labelledby="bonus-tab">
					        		<br/>
									<div class="row" >
										<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
											<div id="bonusTicketDiv" class="input-group">
												<span class="input-group-addon">Nº Bono</span>
												<input id="bonusTicket" type="text" class="form-control">
											</div>
										</div>
										<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
											<div id="bonusValueDiv" class="input-group input-group-primary">
												<span class="input-group-addon">Monto $</span>
												<input id="bonusValue" type="text" class="form-control onlyNumeric">
											</div>
										</div>
										<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
											<div id="bonusValueCoDiv" class="input-group">
												<span class="input-group-addon">Copago $</span>
												<input id="bonusValueCo" type="text" class="form-control onlyNumeric">
											</div>
										</div>
										<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
											<button type="button" class="btn btn-success" onclick="insertPayment('bonus');">Aceptar</button>
										</div>
									</div>
					        	</div>

					        	<!--CRÉDITO-->
					        	<div role="tabpanel" class="tab-pane fade in" id="credit" aria-labelledby="credit-tab">
					        		<br/>
									<div class="row" >
										<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
											<div id="creditUserDiv" class="input-group">
												<span class="input-group-addon">Emisor</span>
												<input id="creditUser" type="text" class="form-control">
											</div>
										</div>
										<div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
											<div id="creditTicketDiv" class="input-group">
												<span class="input-group-addon">Nº Transacción</span>
												<input id="creditTicket" type="text" class="form-control">
											</div>
										</div>
										<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
											<div id="creditValueDiv" class="input-group input-group-primary">
												<span class="input-group-addon">Monto $</span>
												<input id="creditValue" type="text" class="form-control onlyNumeric">
											</div>
										</div>
									</div>
									<br/>
									<div class="row">
										<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
											<div id="creditDateDiv" class="input-group input-group-primary">
												<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>&nbsp;Fecha</span>
												<input id="creditDate" class="datepicker"></input>
											</div>
										</div>
										<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
											<div id="creditBankDiv" class="input-group input-group-primary">
												<span class="input-group-addon">Banco</span>
												<input id="creditBank" type="text" class="form-control">
											</div>
										</div>
										<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
											<button type="button" class="btn btn-success" onclick="insertPayment('credit');">Aceptar</button>
										</div>
									</div>
					        	</div>

					        	<!--DÉBITO-->
					        	<div role="tabpanel" class="tab-pane fade in" id="debit" aria-labelledby="debit-tab">
					        		<br/>
									<div class="row" >
										<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
											<div id="debitUserDiv" class="input-group">
												<span class="input-group-addon">Emisor</span>
												<input id="debitUser" type="text" class="form-control">
											</div>
										</div>
										<div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
											<div id="debitTicketDiv" class="input-group">
												<span class="input-group-addon">Nº Transacción</span>
												<input id="debitTicket" type="text" class="form-control">
											</div>
										</div>
										<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
											<div id="debitValueDiv" class="input-group input-group-primary">
												<span class="input-group-addon">Monto $</span>
												<input id="debitValue" type="text" class="form-control onlyNumeric">
											</div>
										</div>
									</div>
									<br/>
									<div class="row">
										<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
											<div id="debitDateDiv" class="input-group input-group-primary">
												<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>&nbsp;Fecha</span>
												<input id="debitDate" class="datepicker"></input>
											</div>
										</div>
										<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
											<div id="debitBankDiv" class="input-group input-group-primary">
												<span class="input-group-addon">Banco</span>
												<input id="debitBank" type="text" class="form-control">
											</div>
										</div>
										<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
											<button type="button" class="btn btn-success" onclick="insertPayment('debit');">Aceptar</button>
										</div>
									</div>
					        	</div>

					        	<!--CHEQUE-->
					        	<div role="tabpanel" class="tab-pane fade in" id="cheque" aria-labelledby="cheque-tab">
					        		<br/>
									<div class="row" >
										<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
											<div id="chequeUserDiv" class="input-group">
												<span class="input-group-addon">Emisor</span>
												<input id="chequeUser" type="text" class="form-control">
											</div>
										</div>
										<div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
											<div id="chequeTicketDiv" class="input-group">
												<span class="input-group-addon">Nº Cheque</span>
												<input id="chequeTicket" type="text" class="form-control">
											</div>
										</div>
										<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
											<div id="chequeValueDiv" class="input-group input-group-primary">
												<span class="input-group-addon">Monto $</span>
												<input id="chequeValue" type="text" class="form-control onlyNumeric">
											</div>
										</div>
									</div>
									<br/>
									<div class="row">
										<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
											<div id="chequeDateDiv" class="input-group input-group-primary">
												<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>&nbsp;Fecha</span>
												<input id="chequeDate" class="datepicker"></input>
											</div>
										</div>
										<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
											<div id="chequeBankDiv" class="input-group input-group-primary">
												<span class="input-group-addon">Banco</span>
												<input id="chequeBank" type="text" class="form-control">
											</div>
										</div>
										<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
											<button type="button" class="btn btn-success" onclick="insertPayment('cheque');">Aceptar</button>
										</div>
									</div>
					        	</div>

					        	<!--GARANTÍA-->
					        	<div role="tabpanel" class="tab-pane fade in" id="warranty" aria-labelledby="warranty-tab">
					        		<br/>
									<div class="row" >
										<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
											<div class="input-group">
												<span class="input-group-addon">Observaciones</span>
												<input id="warrantyObservation" type="text" class="form-control">
											</div>
										</div>
										<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
											<div class="input-group input-group-primary">
												<span class="input-group-addon">Monto $</span>
												<input id="warrantyValue" type="text" class="form-control onlyNumeric">
											</div>
										</div>
										<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
											<div id="warrantyDateDiv" class="input-group input-group-primary">
												<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>&nbsp;Fecha</span>
												<input id="warrantyDate" class="datepicker"></input>
											</div>
										</div>
										<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
											<button type="button" class="btn btn-success" onclick="insertPayment('warranty');">Aceptar</button>
										</div>
										<div class="col-md-5"></div>
									</div>
					        	</div>
						      
						    </div>
							<br/>

						</div>
					</div>
					<!--DETALLE-->
					<div class="panel panel-primary">
						<div class="panel-heading ">Pagos Ingresados:</div>
						<div id="paymentDetail" class="panel-body" style="min-height: 30%; max-height: 30%;overflow-y: scroll;">
							
						</div>
					</div>
				</div>
				<!--RESUMEN DE PAGO-->
				<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
					<div class="panel panel-primary">
						<div class="panel-heading ">Resumen:</div>
						<div class="panel-body">
							<table class="table">
								<tr>
									<td>Efectivo</td>
									<td id="cashTotal">0</td>
								</tr>
								<tr>
									<td>Bono</td>
									<td id="bonusTotal">0</td>
								</tr>
								<tr>
									<td>Copago</td>
									<td id="bonusCoTotal">0</td>
								</tr>
								<tr>
									<td>Crédito</td>
									<td id="creditTotal">0</td>
								</tr>
								<tr>
									<td>Débito</td>
									<td id="debitTotal">0</td>
								</tr>
								<tr>
									<td>Cheque</td>
									<td id="chequeTotal">0</td>
								</tr>
								<tr>
									<td>Garantía</td>
									<td id="warrantyTotal">0</td>
								</tr>
								<tr>
									<td colspan="2"></td>
								</tr>
								<tr class="info">
									<th>TOTAL</th>
									<th id="total" data-toggle="tooltip" title="El monto ingresado es mayor al total a Pagar">0</th>
								</tr>
								<!--<tr>
									<td colspan="2"></td>
								</tr>
								<tr class='warning'>
									<td>Monto Abonado</td>
									<td id="abonoTotal">0</td>
								</tr>
								<tr class='warning'>
									<td>
										<button class="btn btn-success">+</button>
										<button class="btn btn-danger">-</button>
									</td>
									<td id="abonoTotal">
										<input type="text" class="form-control onlyNumeric" style="width: 70px;"></input>
									</td>
								</tr>-->
							</table>
						</div>
					</div>
					<button type="button" class="btn btn-success btn-lg" onclick="savePayment();" style="height: 15%;width: 100%;">Realizar Pago</button>
				</div>
			</div>
		</div>

		<div id="modalEmpty" class="modal fade " tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="row">
						<h4 id="modalMsg" class="text-center">Faltan datos a ingresar<h4>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>