<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Reserva de Equipamento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    <script>
        $(document).ready(function () {
            const hoje = new Date().toISOString().split('T')[0];
            $('#saida').val(hoje);

            function atualizarValorLocacao() {
                const mensal = parseFloat($('#valorMensal_equipamento_hidden').val()) || 0;
                const quinzenal = parseFloat($('#valorQuinzenal_equipamento_hidden').val()) || 0;

                $('#labelMensal').text(`Selecionar (Mensal R$ ${mensal.toFixed(2).replace('.', ',')})`);
                $('#labelQuinzenal').text(`Selecionar (Quinzenal R$ ${quinzenal.toFixed(2).replace('.', ',')})`);
            }

            $("input[name='cpf']").autocomplete({
                source: function (request, response) {
                    $.ajax({
                        url: "buscar_cliente_autocomplete.php",
                        data: { term: request.term },
                        dataType: "json",
                        success: function (data) {
                            response(data);
                        }
                    });
                },
                minLength: 3,
                select: function (event, ui) {
                    $.ajax({
                        type: "POST",
                        url: "buscar_cliente.php",
                        data: { cpf: ui.item.value },
                        success: function (response) {
                            var dados = JSON.parse(response);
                            $("#nome_cliente").val(dados.nome || '');
                            $("#endereco_cliente").val(dados.endereco || '');
                            $("#complemento_cliente").val(dados.complemento || '');
                            $("#email_cliente").val(dados.email || '');
                            $("#cep_cliente").val(dados.cep || '');
                        }
                    });
                }
            });

            $("input[name='codigoEquipamento']").autocomplete({
                source: function (request, response) {
                    $.ajax({
                        url: "buscar_equipamento_autocomplete.php",
                        data: { term: request.term },
                        dataType: "json",
                        success: function (data) {
                            response(data);
                        }
                    });
                },
                minLength: 1,
                select: function (event, ui) {
                    $.ajax({
                        type: "POST",
                        url: "buscar_equipamento.php",
                        data: { codigoEquipamento: ui.item.value },
                        success: function (response) {
                            var dados = JSON.parse(response);
                            $("#marca_equipamento").val(dados.marca || '');
                            $("#modelo_equipamento").val(dados.modelo || '');
                            $("#peso_equipamento").val(dados.peso || '');
                            $("#valorCaucao_equipamento").val(dados.valorCaucao || '');
                            $("#valorMensal_equipamento_hidden").val(dados.valorMensal || '');
                            $("#valorQuinzenal_equipamento_hidden").val(dados.valorQuinzenal || '');

                            $("#valorMensal_exibido").val('R$ ' + parseFloat(dados.valorMensal).toFixed(2).replace('.', ','));
                            $("#valorQuinzenal_exibido").val('R$ ' + parseFloat(dados.valorQuinzenal).toFixed(2).replace('.', ','));

                            atualizarValorLocacao();
                        }
                    });
                }
            });

            $('input[name="tipoLocacao"]').on('change', function () {
                atualizarValorLocacao();

                const dataSaida = $('#saida').val();
                if (!dataSaida) return;

                const partesData = dataSaida.split('-');
                const dataBase = new Date(partesData[0], partesData[1] - 1, partesData[2]);

                let diasParaAdicionar = 0;
                if ($('#locacaoMensal').is(':checked')) {
                    diasParaAdicionar = 30;
                } else if ($('#locacaoQuinzenal').is(':checked')) {
                    diasParaAdicionar = 15;
                }

                if (diasParaAdicionar > 0) {
                    dataBase.setDate(dataBase.getDate() + diasParaAdicionar);
                    const novaData = dataBase.toISOString().split('T')[0];
                    $('#retorno').val(novaData);
                }
            });
        });
    </script>

    <style>
        .tab-content {
            padding: 20px;
        }
        .tab-pane {
            display: none;
        }
        .tab-pane.active {
            display: block;
        }
        .form-control[readonly] {
            background-color: #f9f9f9;
        }
        .btn-icon {
            margin-right: 8px;
        }
        .icon-validate {
            color: #28a745;
        }
    </style>
</head>
<body class="bg-light">
<div class="container py-5">
    <h2 class="mb-4 text-center">Reserva de Equipamento</h2>

    <ul class="nav nav-tabs" id="formTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active" id="clienteTab" data-bs-toggle="tab" href="#cliente" role="tab" aria-controls="cliente" aria-selected="true">
                <i class="bi bi-person"></i> Cliente
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="equipamentoTab" data-bs-toggle="tab" href="#equipamento" role="tab" aria-controls="equipamento" aria-selected="false">
                <i class="bi bi-laptop"></i> Equipamento
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="reservaTab" data-bs-toggle="tab" href="#reserva" role="tab" aria-controls="reserva" aria-selected="false">
                <i class="bi bi-calendar"></i> Reserva
            </a>
        </li>
    </ul>

    <form name="res" action="reser_210.php" method="post" class="bg-white p-4 rounded shadow-sm">
        <div class="tab-content" id="formTabsContent">
            <!-- CLIENTE -->
            <div class="tab-pane fade show active" id="cliente" role="tabpanel" aria-labelledby="clienteTab">
                <div class="mb-3">
                    <label for="cpf_cliente" class="form-label">CPF do Cliente</label>
                    <input type="text" name="cpf" id="cpf_cliente" class="form-control" placeholder="Digite o CPF" required>
                    <i class="bi bi-person-check icon-validate" id="cpfValid"></i>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nome</label>
                    <input type="text" id="nome_cliente" class="form-control" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Endereço</label>
                    <input type="text" id="endereco_cliente" class="form-control" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Complemento</label>
                    <input type="text" id="complemento_cliente" class="form-control" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="text" id="email_cliente" class="form-control" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">CEP</label>
                    <input type="text" id="cep_cliente" class="form-control" readonly>
                </div>
            </div>

            <!-- EQUIPAMENTO -->
            <div class="tab-pane fade" id="equipamento" role="tabpanel" aria-labelledby="equipamentoTab">
                <div class="mb-3">
                    <label for="codigo_equipamento" class="form-label">Código do Equipamento</label>
                    <input type="text" name="codigoEquipamento" id="codigo_equipamento" class="form-control" placeholder="Digite o código" required>
                    <i class="bi bi-hdd-fill icon-validate" id="equipamentoValid"></i>
                </div>
                <div class="mb-3">
                    <label class="form-label">Marca</label>
                    <input type="text" id="marca_equipamento" class="form-control" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Modelo</label>
                    <input type="text" id="modelo_equipamento" class="form-control" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Peso Recomendado</label>
                    <input type="text" id="peso_equipamento" class="form-control" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Valor Caução</label>
                    <input type="text" id="valorCaucao_equipamento" class="form-control" readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label">Valores de Locação</label>
                    <div class="row">
                        <div class="col-md-6">
                        <label class="form-label">Valor Mensal</label>
                            <input type="text" id="valorMensal_exibido" class="form-control" placeholder="Valor Mensal" readonly>
                        </div>
                        <label class="form-label">Valor Quinzenal</label>
                        <div class="col-md-6">
                            <input type="text" id="valorQuinzenal_exibido" class="form-control" placeholder="Valor Quinzenal" readonly>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RESERVA -->
            <div class="tab-pane fade" id="reserva" role="tabpanel" aria-labelledby="reservaTab">
                <div class="mb-3">
                    <label for="saida" class="form-label">Data de Saída</label>
                    <input type="date" name="saida" id="saida" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="retorno" class="form-label">Data de Devolução</label>
                    <input type="date" name="retorno" id="retorno" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tipo de Locação</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="tipoLocacao" id="locacaoMensal" value="mensal" required>
                        <label class="form-check-label" for="locacaoMensal" id="labelMensal">
                            Selecionar (Mensal R$ 0,00)
                        </label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="tipoLocacao" id="locacaoQuinzenal" value="quinzenal" required>
                        <label class="form-check-label" for="locacaoQuinzenal" id="labelQuinzenal">
                            Selecionar (Quinzenal R$ 0,00)
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <input type="hidden" id="valorMensal_equipamento_hidden" name="valorMensal">
        <input type="hidden" id="valorQuinzenal_equipamento_hidden" name="valorQuinzenal">

        <div class="d-flex justify-content-between">
            <button type="submit" name="butreg" class="btn btn-primary">Gravar</button>
            <a href="reser_100.php" class="btn btn-secondary">Voltar</a>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
