<!DOCTYPE html>
<html lang="pt-BR">
<head>
	<title>Usuários influentes</title>
</head>
<body>
	<button onclick="GetData()">GET</button>
	<button onclick="ProcessRequest()">Process</button>
	<button onclick="MakeTheGraph(myArrayGraph)">Gráfico</button>
	<input type="number" id="brand" name="brand" min="1" max="5" value="1"/>
	<button onclick="Filter()">filter</button>
    <div id="columnchart_material" style="width: 1024px; height: 2048px;"></div>
</body>
<script type="text/javascript" src="./js/charts/loader.js"></script>
<script type="text/javascript">
	var graphData 		= 	new Array();
	var count 			=	0;
	var userLabels		=	[];
	var userValues		=	[];
	var myArrayGraph	= 	[];
	var arrayGraphUsersValuesByBrand 	= [];
	var arrayGraphUsersValuesByFilter 	= [];

	function GetData(){
		GetInteractions();
		GetUsers();
		GetBrands();
	}

	function GetInteractions()
	{
		var Url = "http://108.179.252.94/~fulltask/ericfulltask.com/API/index.php?token=4123eebdcffc379db535a63184b836e0&file=interactions";
		xmlHttpInteractions = new XMLHttpRequest();
		xmlHttpInteractions.open( "GET", Url, true );
		xmlHttpInteractions.send( null );
	}

	function GetUsers()
	{
		var Url = "http://108.179.252.94/~fulltask/ericfulltask.com/API/index.php?token=4123eebdcffc379db535a63184b836e0&file=users";
		xmlHttpUsers = new XMLHttpRequest();
		xmlHttpUsers.open( "GET", Url, true );
		xmlHttpUsers.send( null );
	}

	function GetBrands()
	{
		var Url = "http://108.179.252.94/~fulltask/ericfulltask.com/API/index.php?token=4123eebdcffc379db535a63184b836e0&file=brands";
		xmlHttpBrands = new XMLHttpRequest();
		xmlHttpBrands.open( "GET", Url, true );
		xmlHttpBrands.send( null );
	}

	function ProcessRequest()
	{
		interactions 	= JSON.parse(xmlHttpInteractions.responseText);
		users 			= JSON.parse(xmlHttpUsers.responseText);
		brands 			= JSON.parse(xmlHttpBrands.responseText);

		users.forEach(GroupInteractions);
		graphData = Order(graphData);
		userLabels.push("Usuários");
		brands.forEach(GraphArrayLabels);
		myArrayGraph[0] = [];
		myArrayGraph.push(userLabels);
		GroupInteractionsByBrands();
		graphData = Union(graphData);
		graphData = InsertAllBrands(graphData);
		GraphArrayValues(graphData);
		myArrayGraph.shift();
	}

	function Filter(){
		GraphArrayValuesByBrand(myArrayGraph,document.getElementById('brand').value);
	}

	function GroupInteractions(user){
		count = 0;
		interactions.forEach(function(interaction){
			if( user.id == interaction.user )
			{
				count++;
			}
		});
		var myObject = { "userId" : user.id, "name" : user.name ,"interactions" : count , "brands" : new Array() };
		graphData.push(myObject);
	}

	function InsertAllBrands(array){
		array.forEach(function(user){
			for(i=1;i<6;i++)
			{
				if( isNaN(user.brands[i]) ) { user.brands[i] = 0; }
			}
		});
		return array;
	}

	function GroupInteractionsByBrands(){
		users.forEach(function(user){
			arrayGraphUsersValuesByBrand[user.id] = [];
			brands.forEach(function(brand){
				interactions.forEach(function(interaction){
					if( (user.id == interaction.user) && (brand.id == interaction.brand) )
					{
						if( isNaN(arrayGraphUsersValuesByBrand[user.id][brand.id]) ){ arrayGraphUsersValuesByBrand[user.id][brand.id] = 0};
						arrayGraphUsersValuesByBrand[user.id][brand.id]++;
					}
				});
			});
		});
	}

	function Union(array){
		array.forEach(function(user){
			user.brands = arrayGraphUsersValuesByBrand[user.userId];
		})
		return array;
	}

	function Order(array){
		array.sort(function(a,b) {
			if(a.interactions > b.interactions) return -1;
			if(a.interactions < b.interactions) return 1;
			return 0;
		});
		return array;
	}

	function OrderFilter(array){
		array.sort(function(a,b) {
			if(a[1] > b[1]) return -1;
			if(a[1] < b[1]) return 1;
			return 0;
		});
		return array;
	}

	function GraphArrayLabels(brands){
		userLabels.push(brands.name);
	}

	function GraphArrayValues(array){
		array.forEach(function(user){
			var userArray = [];
			userArray.push(user.name.first);
			user.brands.forEach(function(value){
				userArray.push(value);
			});
			myArrayGraph.push(userArray);
		});
	}

	function GraphArrayValuesByBrand(array,selectedBrand){
		arrayGraphUsersValuesByFilter = [];
		array.forEach(function(user){
			var userArray = [];
			var count = 0 ;
			user.forEach(function (item) {
				if( count  == 0 || count == selectedBrand)
				{
					userArray.push(item);
				}
				count++;
			});
			arrayGraphUsersValuesByFilter.push(userArray);
		});
		console.log(arrayGraphUsersValuesByFilter);
		MakeTheGraph(arrayGraphUsersValuesByFilter);
	}


	function MakeTheGraph(array){
		google.charts.load('current', {'packages':['bar']});
		google.charts.setOnLoadCallback(drawChart);
		function drawChart() {
			var data = google.visualization.arrayToDataTable(array);

			var options = {
				chart: {
				title: 'Avaliação Desenvolvedor Fron-End Prática',
				subtitle: 'Usuários Influentes',
				},
				bars: 'horizontal'
			};

			var chart = new google.charts.Bar(document.getElementById('columnchart_material'));

			chart.draw(data, options);
		}
	}
	</script>
</html>
