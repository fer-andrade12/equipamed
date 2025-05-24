<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Locação de Equipamentos Médicos</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Estilos personalizados -->
    <style>
        body {
            background: linear-gradient(to right, #e0f7fa, #ffffff), url('hospital_background.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #333;
            font-family: 'Arial', sans-serif;
        }

        .navbar {
            background-color: #006f72;
        }

        .navbar-nav .nav-link {
            font-size: 1.2rem;
        }

        .navbar {
            background-color: #006f72;
            min-height: 70px; /* Aumenta a altura */
            padding-top: 50px; /* Espaçamento superior */
            padding-bottom: 10px; /* Espaçamento inferior */
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .text-center h1 {
            color: #006f72;
            font-size: 2.5rem;
        }

        .text-center p {
            color: #555;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="#"><i class="bi bi-hospital"></i> Locação de Equipamentos</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMenu">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">
                        <i class="bi bi-house"></i> Início
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="condut_100.php">
                        <i class="bi bi-people"></i> Clientes
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="auto_100.php">
                        <i class="bi bi-gear"></i> Equipamentos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="reser_100.php">
                        <i class="bi bi-calendar-check"></i> Reservas
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="listar_cobrancas.php">
                        <i class="bi bi-currency-dollar"></i> Cobrança
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Conteúdo -->
<div class="container text-center">
    <h1><i class="bi bi-hospital"></i> Sistema de Locação de Equipamentos Médicos</h1>
    <p class="lead">Bem-vindo! Escolha uma opção no menu acima para começar.</p>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
