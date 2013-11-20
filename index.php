<!DOCTYPE html> 
<html> 
	<head> 
		<link href="css/kendo.metro.min.css"rel="stylesheet"> 
		<link href="css/kendo.common.min.css" rel="stylesheet">
		<script src="js/jquery.min.js"></script>
		<script src="js/kendo.web.min.js"></script>
	</head>
	<body>
		<div id="grid"></div>
		<script>
			$(function() {
				$("#grid").kendoGrid({
					dataSource: {
						transport: {
							read: "data/employees.php",
							update: {
								url: "data/employees.php",
								type: "POST"
							}
 						},
						error: function(e) {
							alert(e.responseText);
						},
						schema: {
							data: "data",
							model: {
								id: "EmployeeID",
								fields: {
									FirstName: { editable: false },
									LastName: { validation: { required: true} }
								}
							}
						}
					},
					columns: [{ field: "FirstName" }, { field: "LastName" }],
					detailTemplate: kendo.template($("#template").html()),
					detailInit: detailInit,
					editable: true,
					navigable: true,  // enables keyboard navigation in the grid
					toolbar: [ "save", "cancel" ]  // adds save and cancel buttons
 				});
			});
		</script> 
		<script type="text/x-kendo-template" id="template">
			<div>
				<div style="margin-bottom: 10px;">
					<input id="territory_#= data.EmployeeID #" class="comboBox" />
  					<button class="k-button add-territory" data-employee-id="#= data.EmployeeID #"
						onclick="addTerritory(this)" >Add</button>
			</div>
			<div class="subgrid"></div>
		</script>
		<script> 
			function detailInit(e) {
				// get a reference to the current row being initialized 
				var detailRow = e.detailRow;

				employeeTerritoriesDS = new kendo.data.DataSource({
					transport: {
						read: "data/employeeTerritories.php",
						create: {
							url: "data/employeeTerritories.php",
							type: "PUT"
						},
					},
					schema: {
						data: "data",
						model: {
							id: "EmployeeTerritoryID",
							fields: {
								TerritoryDescription: { editable: false }
							}
						}
					},
					serverFiltering: true,
					filter: { field: "EmployeeID", operator: "eq", value: e.data.EmployeeID }
				});

				// create a subgrid for the current detail row, getting territory data for this employee
				detailRow.find(".subgrid").kendoGrid({
					dataSource: employeeTerritoriesDS,
					columns: [{ title: "Territories", field: "TerritoryDescription" }],
					editable: true,
					toolbar: [ "save" ]

				});

				// create the datasource
				territoriesDS = new kendo.data.DataSource({
					transport: {
						read: "data/territories.php?EmployeeID=" + e.data.EmployeeID
					},
					schema: {
						data: "data",
						model: {
							id: "TerritoryID"
						}
					}

				});

				// create the autocomplete
				detailRow.find(".comboBox").kendoComboBox({
					dataSource: territoriesDS,
					dataTextField: "TerritoryDescription",
					dataValueField: "TerritoryID"
				});
			}

			var addTerritory = function(sender) {
				// get the employee id off the data-employee-id attribute of the button
				var employeeId = $(sender).data("employee-id");

				// get a reference to the combobox which contains the selected item
				var comboBox = $("#territory_" + employeeId).data("kendoComboBox");
   				// add the item to the datasource - it is thusly added to the grid
   				employeeTerritoriesDS.add({ EmployeeID: employeeId, TerritoryDescription: comboBox.text(),  TerritoryID: comboBox.value() });

  				// remove the current item from the combobox - it's no longer a valid selection
				territoriesDS.remove(comboBox.value());

				// clear the text of the combobox
				comboBox.text("");
			}
		</script>
	</body> 
</html>
