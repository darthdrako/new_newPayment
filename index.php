<?php

//$calendar = $_REQUEST['calendar'];
$calendar = 217141;
?>

<html>
	<head>
		<meta charset="utf-8">
		<script src="js/jquery.min.js"></script>
		<script src="js/bootstrap/js/bootstrap.min.js"></script>
		<link href="js/bootstrap/css/bootstrap.min.css" rel="stylesheet">
		<link href="js/font-awesome-4.2.0/css/font-awesome.min.css" rel="stylesheet">
		<script>
			var countCash = 0, countBonus = 0,countCredit = 0,countDebit = 0,countCheque = 0,countWarranty = 0;
			var paymentArray = [];

			$(document).ready(function() {
				var calendar = '<?php echo $calendar; ?>';
				detailCalendar(calendar);
				$('#paymentSelect a').click(function (e) {
					e.preventDefault()
					$(this).tab('show')
				});
				
				$(".onlyNumeric").keypress(function(e) {
				 	validateNumber(e);
				});

			});
			function detailCalendar (calendar) {
				var html = '';
				var popoverTable = '';
				$.post('phps/detailCalendar.php', {calendar: calendar}, function(data, textStatus, xhr) {
					console.log(data.exams);
					for (var i = 0; i < data.exams.length; i++) {
						if( i == 0 ) popoverTable += ''+(i+1)+'.- '+data.exams[i].name+'';
						else popoverTable += '<br>'+(i+1)+'.- '+data.exams[i].name+'';
					};
					html += '<td>'+data.paciente+'</td>';
					html += '<td>'+data.date+'</td>';
					html += '<td>'+data.hour+'</td>';
					html += '<td><button type="button" class="btn btn-md btn-danger" data-placement="auto top" data-trigger="hover" data-toggle="popover" data-html="true" title="Examenes y Convenios" data-content="'+popoverTable+'">Examenes/Convenios</button></td>';
					$('#detail').html(html);
					$('[data-toggle="popover"]').popover()
				});
			}
			function insertPayment(type){
				var detail='';
				var closeButton = '<button type="button" class="close" onclick="deletePayment(this)" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>';
				if(type=="cash"){
					if($("#cashValue").val()!='' && $("#cashTicket").val()!=''){
						detail='<div id="cash-'+countCash+'" class="alert alert-success alert-dismissible fade in" role="alert">'+closeButton+'</button><strong>Efectivo</strong><br/> Boleta: '+$("#cashTicket").val()+', Valor: '+$("#cashValue").val()+'</div>';
						paymentArray.push({type:"cash", id: countCash, ticket: $("#cashTicket").val(), value: $("#cashValue").val()});
						countCash++;
						calculate();
						$("#cashValue").val(''); 
						$("#cashTicket").val(''); 
					}else{
						/*var msg = 'Debe ingresar:';
						if($("#cashTicket").val()=='') msg+='<br/>- Nº Boleta';
						if($("#cashValue").val()=='') msg+='<br/>- Monto';
						$("#modalMsg").html(msg);*/
						$("#modalEmpty").modal('show');
					}

				}else if(type=="bonus"){
					if($("#bonusTicket").val()!='' && $("#bonusValue").val()!='' && $("#bonusValueCo").val()!=''){
						detail='<div id="bonus-'+countBonus+'" class="alert alert-info alert-dismissible fade in" role="alert">'+closeButton+'<strong>Bono</strong><br/> Nº '+$("#bonusTicket").val()+', Valor Bono: '+$("#bonusValue").val()+', Copago: '+$("#bonusValueCo").val()+'</div>';
						paymentArray.push({type:"bonus", id: countBonus, ticket: $("#bonusTicket").val(), value: $("#bonusValue").val(), covalue: $("#bonusValueCo").val()});
						countBonus++;
						calculate();
						$("#bonusTicket").val(''); 
						$("#bonusValue").val(''); 
						$("#bonusValueCo").val(''); 
					}else{
						$("#modalEmpty").modal('show');
					}

				}else if(type=="credit"){
					if($("#creditTicket").val()!='' && $("#creditValue").val()!='' && $("#creditUser").val()!=''){
						detail='<div id="credit-'+countCredit+'" class="alert alert-info alert-dismissible fade in" role="alert">'+closeButton+'<strong>Crédito</strong><br/> Emisor: '+$("#creditUser").val()+', Nº de Transacción: '+$("#creditTicket").val()+', Valor: '+$("#creditValue").val()+'</div>';
						paymentArray.push({type:"credit", id: countCredit, ticket: $("#creditTicket").val(), value: $("#creditValue").val(), user: $("#creditUser").val()});
						countCredit++;
						calculate();
						$("#creditTicket").val(''); 
						$("#creditValue").val(''); 
						$("#creditUser").val(''); 
					}else{
						$("#modalEmpty").modal('show');
					}

				}else if(type=="debit"){
					if($("#debitTicket").val()!='' && $("#debitValue").val()!='' && $("#debitUser").val()!=''){
						detail='<div id="debit-'+countDebit+'" class="alert alert-warning alert-dismissible fade in" role="alert">'+closeButton+'<strong>Débito</strong><br/> Emisor: '+$("#debitUser").val()+', Nº de Transacción: '+$("#debitTicket").val()+', Valor: '+$("#debitValue").val()+'</div>';
						paymentArray.push({type:"debit", id: countDebit, ticket: $("#debitTicket").val(), value: $("#debitValue").val(), user: $("#debitUser").val()});
						countDebit++;
						calculate();
						$("#debitTicket").val(''); 
						$("#debitValue").val(''); 
						$("#debitUser").val(''); 
					}else{
						$("#modalEmpty").modal('show');
					}

				}else if(type=="cheque"){
					if($("#chequeTicket").val()!='' && $("#chequeValue").val()!='' && $("#chequeUser").val()!=''){
						detail='<div id="cheque-'+countCheque+'" class="alert alert-warning alert-dismissible fade in" role="alert">'+closeButton+'<strong>Cheque</strong><br/> Emisor: '+$("#chequeUser").val()+', Nº de Transacción: '+$("#chequeTicket").val()+', Valor: '+$("#chequeValue").val()+'</div>';
						paymentArray.push({type:"cheque", id: countCheque, ticket: $("#chequeTicket").val(), value: $("#chequeValue").val(), user: $("#chequeUser").val()});
						countCheque++;
						calculate();
						$("#chequeTicket").val(''); 
						$("#chequeValue").val('');  
						$("#chequeUser").val(''); 
					}else{
						$("#modalEmpty").modal('show');
					}

				}else if(type=="warranty"){
					if($("#warrantyObservation").val()!='' && $("#warrantyValue").val()!='' && $("#warrantyDate").val()!=''){
						detail='<div id="warranty-'+countWarranty+'" class="alert alert-danger alert-dismissible fade in" role="alert">'+closeButton+'<strong>Garantía</strong><br/> Observaciones: '+$("#warrantyObservation").val()+', Valor: '+$("#warrantyValue").val()+', Fecha: '+$("#warrantyDate").val()+'</div>';
						paymentArray.push({type:"warranty", id: countWarranty, observation: $("#warrantyObservation").val(), value: $("#warrantyValue").val(), date: $("#warrantyDate").val()});
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
			}

			function deletePayment(payment){
				var paymentId = $(payment).parent().attr('id').split('-');
				for(i=0;i<paymentArray.length;i++){
					if(paymentArray[i].type==paymentId[0] && paymentArray[i].id==paymentId[1]){
						paymentArray.splice(i,1);
						break;
					}
				}
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

			function savePayment(){
				if(paymentArray.length>0){
					for(i=0;i<paymentArray.length;i++){
						console.log(paymentArray[i]);

					}
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
									</tr>
								</thead>
								<tbody>
									<tr id="detail">
									</tr>
								</tbody>
							</table>
					</div>
				</div>
				<div class="col-md-9">
					<div class="panel panel-primary">
						<div class="panel-heading ">Información de Pago</div>
						<div class="panel-body">
							<ul id="paymentSelect" class="nav nav-tabs nav-justified">
								<li role="presentation" class="active"><a href="#cash" role="tab" id="cash-tab" data-toggle="tab" aria-controls="cash" aria-expanded="true"><span class="fa fa-money"></span>&nbsp;Efectivo</a></li>
								<li role="presentation"><a href="#bonus"><span class="fa fa-list-alt"></span>&nbsp;Bono</a></li>
								<li role="presentation"><a href="#credit"><span class="fa fa-credit-card"></span>&nbsp;Crédito</a></li>
								<li role="presentation"><a href="#debit"><span class="glyphicon glyphicon-credit-card"></span>&nbsp;Débito</a></li>
								<li role="presentation"><a href="#cheque"><span class="fa fa-ticket"></span>&nbsp;Cheque</a></li>
								<li role="presentation"><a href="#warranty"><span class="glyphicon glyphicon-transfer"></span>&nbsp;Garantía</a></li>
							</ul>
							
							<!--EFECTIVO-->
							<div id="myTabContent" class="tab-content">
								<div role="tabpanel" class="tab-pane fade in active" id="cash" aria-labelledby="cash-tab">
									<br/>		
									<div class="row" >
										<div class="col-md-5">
											<div class="input-group">
												<span class="input-group-addon">Nº Boleta</span>
												<input id="cashTicket" type="text" class="form-control">
											</div>
										</div>
										<div class="col-md-5">
											<div class="input-group input-group-primary">
												<span class="input-group-addon">Monto $</span>
												<input id="cashValue" type="text" class="form-control onlyNumeric">
											</div>
										</div>
										<div class="col-md-2">
											<button type="button" class="btn btn-success" onclick="insertPayment('cash');">Aceptar</button>
										</div>
									</div>
					        	</div>
					        	<!--BONO-->
					        	<div role="tabpanel" class="tab-pane fade in" id="bonus" aria-labelledby="bonus-tab">
					        		<br/>
									<div class="row" >
										<div class="col-md-4">
											<div class="input-group">
												<span class="input-group-addon">Nº Bono</span>
												<input id="bonusTicket" type="text" class="form-control">
											</div>
										</div>
										<div class="col-md-3">
											<div class="input-group input-group-primary">
												<span class="input-group-addon">Monto $</span>
												<input id="bonusValue" type="text" class="form-control onlyNumeric">
											</div>
										</div>
										<div class="col-md-3">
											<div class="input-group">
												<span class="input-group-addon">Copago $</span>
												<input id="bonusValueCo" type="text" class="form-control onlyNumeric">
											</div>
										</div>
										<div class="col-md-1">
											<button type="button" class="btn btn-success" onclick="insertPayment('bonus');">Aceptar</button>
										</div>
									</div>
					        	</div>

					        	<!--CRÉDITO-->
					        	<div role="tabpanel" class="tab-pane fade in" id="credit" aria-labelledby="credit-tab">
					        		<br/>
									<div class="row" >
										<div class="col-md-3">
											<div class="input-group">
												<span class="input-group-addon">Emisor</span>
												<input id="creditUser" type="text" class="form-control">
											</div>
										</div>
										<div class="col-md-4">
											<div class="input-group">
												<span class="input-group-addon">Nº Transacción</span>
												<input id="creditTicket" type="text" class="form-control">
											</div>
										</div>
										<div class="col-md-3">
											<div class="input-group input-group-primary">
												<span class="input-group-addon">Monto $</span>
												<input id="creditValue" type="text" class="form-control onlyNumeric">
											</div>
										</div>
										<div class="col-md-1">
											<button type="button" class="btn btn-success" onclick="insertPayment('credit');">Aceptar</button>
										</div>
									</div>
					        	</div>

					        	<!--DÉBITO-->
					        	<div role="tabpanel" class="tab-pane fade in" id="debit" aria-labelledby="debit-tab">
					        		<br/>
									<div class="row" >
										<div class="col-md-3">
											<div class="input-group">
												<span class="input-group-addon">Emisor</span>
												<input id="debitUser" type="text" class="form-control">
											</div>
										</div>
										<div class="col-md-4">
											<div class="input-group">
												<span class="input-group-addon">Nº Transacción</span>
												<input id="debitTicket" type="text" class="form-control">
											</div>
										</div>
										<div class="col-md-3">
											<div class="input-group input-group-primary">
												<span class="input-group-addon">Monto $</span>
												<input id="debitValue" type="text" class="form-control onlyNumeric">
											</div>
										</div>
										<div class="col-md-1">
											<button type="button" class="btn btn-success" onclick="insertPayment('debit');">Aceptar</button>
										</div>
										<div class="col-md-2"></div>
									</div>
					        	</div>

					        	<!--CHEQUE-->
					        	<div role="tabpanel" class="tab-pane fade in" id="cheque" aria-labelledby="cheque-tab">
					        		<br/>
									<div class="row" >
										<div class="col-md-3">
											<div class="input-group">
												<span class="input-group-addon">Emisor</span>
												<input id="chequeUser" type="text" class="form-control">
											</div>
										</div>
										<div class="col-md-4">
											<div class="input-group">
												<span class="input-group-addon">Nº Cheque</span>
												<input id="chequeTicket" type="text" class="form-control">
											</div>
										</div>
										<div class="col-md-3">
											<div class="input-group input-group-primary">
												<span class="input-group-addon">Monto $</span>
												<input id="chequeValue" type="text" class="form-control onlyNumeric">
											</div>
										</div>
										<div class="col-md-1">
											<button type="button" class="btn btn-success" onclick="insertPayment('cheque');">Aceptar</button>
										</div>
										<div class="col-md-2"></div>
									</div>
					        	</div>

					        	<!--GARANTÍA-->
					        	<div role="tabpanel" class="tab-pane fade in" id="warranty" aria-labelledby="warranty-tab">
					        		<br/>
									<div class="row" >
										<div class="col-md-4">
											<div class="input-group">
												<span class="input-group-addon">Observaciones</span>
												<input id="warrantyObservation" type="text" class="form-control">
											</div>
										</div>
										<div class="col-md-3">
											<div class="input-group input-group-primary">
												<span class="input-group-addon">Monto $</span>
												<input id="warrantyValue" type="text" class="form-control onlyNumeric">
											</div>
										</div>
										<div class="col-md-3">
											<div class="input-group">
												<span class="input-group-addon">Fecha de Pago</span>
												<input id="warrantyDate" type="text" class="form-control">
											</div>
										</div>
										<div class="col-md-1">
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
				<div class="col-md-3">
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
									<th id="total">0</th>
								</tr>
								<tr>
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
								</tr>
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