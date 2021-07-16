function reset() {
	$("#cfxdisplay").html("");
};

function checkCFX() {
	let cfx = $("#cfxid").val();
	if (cfx === "" || cfx === null || cfx === undefined) {
		document.getElementById("alerta").classList = 'alert alert-danger';
		$("#alerta").html("<i class='fe fe-alert-triangle'></i> &nbsp; Não deixe nenhum campo em branco!");
	} else {
		$.ajax({
			type: "POST",
			url: "/api/checkCFX",
			data: {
				cfx: cfx,
			},
			success: function(response) {
				let json = JSON.parse(response);
				console.log(json)
				if (json.status === "Y") {
					reset();
					$('#exampleModal').modal('toggle');
					$('#cfxdisplay').append(`
				<b style="font-size:24px" >Detalhes</b> <br><br>
                <b>Nome do servidor: </b> ${json.name} <br>
                <b>IP:Porta: </b> ${json.ipfull} <br><br>
                <b style="font-size:24px" >Detalhes do Servidor </b> <br><br>
                <b>IP: </b> ${json.ip} <br>
                <b>Porta: </b> ${json.porta} <br>
                <b>País: </b> ${json.country} <br>
                <b>ISP: </b> ${json.isp} <br>
                <b>ORG: </b> ${json.org} <br><br>

                <b style="font-size:24px">FiveM Server</b> <br><br>
                <b>Players Online: </b> ${json.players} <br>
                <b>Máximo de Jogadores: </b> ${json.maxplayers} <br>
                <b>Versão do Servidor: </b> ${json.versao} <br>
                <b>Info(JSON): </b> <a href="http://${json.ipfull}/players.json" target="_blank">  http://${json.ipfull}/players.json<br></a>
                `);
				} else if (json.status === "N") {
					document.getElementById("alerta").classList = 'alert alert-danger';
					$("#alerta").html(`<i class='fe fe-alert-triangle'></i> &nbsp; ${json.msg}`);
				} else {
					document.getElementById("alerta").classList = 'alert alert-danger';
					$("#alerta").html("<i class='fe fe-alert-triangle'></i> &nbsp; Tivemos problemas ao realizar sua requisição, aguarde uns segundos e tente novamente! 1");
				}
			},
			error: function(error) {
				let erro = JSON.parse(error);
				console.log(erro);
			}
		});
	}
}
