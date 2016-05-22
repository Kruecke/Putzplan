(function(){ 
/*Wrapper Start*/
	var app = angular.module('constellation', [ ]);

	app.controller('MainController', function($timeout, $scope, $http){
		function Message(color, text){
			$scope.showLog = "dontDisplay";
			if (text == 'loading'){
				$scope.main.msg = " ";
				$scope.messageAlert = 'alert';
				$scope.showLoading='Display';
			} else{
				$scope.messageAlert = 'alert alert-'+color;
				$scope.main.msg = text;
			}
		}
		/* HTTP Requests */
		this.req= function(type){
			$scope.showLog = "dontDisplay";
			/* Collect data */
			Message('info','loading');
			var data = {
				state: "",
				content: {}
			}
			// mux
			switch (type){
				case 'init':
					data.state = 'init';
				break;
				case 'reset':
					data.state = 'reset';
				break;
				case 'save':
					data.state = 'save';
					data.content = { config: $scope.main.config };
				break;
				case 'generate':
					data.state = 'generate';
				break;
				case 'download':
					data.state = 'download';
				break;
			}
			
			/* Actual Request */
		    var request = $http({
		        method: "post",
		        url: window.location.href + "server/putzplanAPI.php",
		        data: data,
		        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
		    });

		    /* Check whether the HTTP Request is successful or not. */
		    request.success(function (data) {
				$scope.showLoading='dontDisplay';
				// demux
				var state = data.state;
				switch (state){
					case 'init':
						$scope.showLoading='dontDisplay';
						if(data.content.datum != "noFile")
							$scope.main.datum = "vom " + data.content.datum;
						$scope.main.config = data.content.config;
					break;
					case 'reset':
						$scope.main.config = data.content.config;
						Message('success','Die Einstellungen wurden erfolgreich zurückgesetzt.');
					break;
					case 'save':
						if(data.content.status == 'success')
							Message('success','Die Einstellungen wurden erfolgreich gespeichert.');
						if(data.content.status == 'failed')
							Message('danger','Die Einstellungen konnten nicht gespeichert werden.');
					break;
					case 'generate':
						if(data.content.status == 'success')
							Message('success','Der Putzplan wurde erfolgreich generiert.');
						if(data.content.status == 'failed')
							Message('danger','Der Putzplan konnte nicht generiert werden. Schau dir die Log Ausgaben an, vielleicht kannst du den Fehler bereits erkennen. Ansonsten melde den Fehler mit möglichst genauer Beschreibung.');
						$scope.main.log = data.content.log;
						$scope.showLog = "Display";
					break;
					case 'download':
						if(data.content.status == 'notavailable')
							Message('warning','Momentan steht kein Download bereit. Möglicherweise muss der Putzplan zunächst generiert werden.');
						if(data.content.status == 'available'){
							Message('success',"Direktlink: " + window.location.href +"server/" +data.content.pdfURL); 
							$scope.pdfURL = window.location.href +"server/" +data.content.pdfURL;
							window.open(window.location.href +"server/" +data.content.pdfURL);
						}
					break;
				}
		    });
		}
		this.req('init');
 	});
/*Wrapper Ende*/
})();
