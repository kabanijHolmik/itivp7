<?php
require_once __DIR__ . "/queries.php";
session_start();
$error = $_SESSION['error'] ?? null;
$success = $_SESSION['success'] ?? null;

// Очищаем сообщения, чтобы они не отображались повторно
unset($_SESSION['error'], $_SESSION['success']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Калькулятор доставки</title>

    <!-- Подключение скомпилированных стилей -->
    <link rel="stylesheet" href="dist/css/styles.css">
</head>

<body>
    <header class="header">
        <div class="container">
            <h1>Калькулятор стоимости доставки</h1>
        </div>
    </header>

    <main class="main">
        <div class="container">
            <?php
            $pickUpPoints = getOffices();
            ?>

            <form id="delivery-form" class="delivery-form" method="POST" action="calculate.php">
                <div class="form-group">
                    <label for="weight">Вес (кг):</label>
                    <input type="text" id="weight" name="weight" placeholder="Введите вес" maxlength="6" required>
                </div>

                <div class="form-group">
                    <label for="size">Объем (м³):</label>
                    <input type="text" id="size" name="size" placeholder="Введите объем" maxlength="6" required>
                </div>

                <div class="form-group">
                    <label for="from-point">Пункт отправления:</label>
                    <select id="from-point" name="from-point" required>
                        <option value="" disabled selected>Выберите пункт отправления</option>
                        <?php if (!empty($pickUpPoints)): ?>
                            <?php foreach ($pickUpPoints as $point): ?>
                                <option value="<?= htmlspecialchars($point['name']) ?>">
                                    <?= htmlspecialchars($point['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="to-point">Пункт назначения:</label>
                    <select id="to-point" name="to-point" required>
                        <option value="" disabled selected>Выберите пункт назначения</option>
                        <?php if (!empty($pickUpPoints)): ?>
                            <?php foreach ($pickUpPoints as $point): ?>
                                <option value="<?= htmlspecialchars($point['name']) ?>">
                                    <?= htmlspecialchars($point['name']) ?>
                                </option>
                            <?php endforeach; ?>

                        <?php endif; ?>
                    </select>
                </div>

                <div class="form-group form-group-checkbox">
                    <input type="checkbox" id="is-fragile" name="is-fragile">
                    <label for="is-fragile">Хрупкий груз</label>
                </div>

                <div class="form-group form-group-checkbox">
                    <input type="checkbox" id="needs-inspection" name="needs-inspection">
                    <label for="needs-inspection">Требуется проверка комплектности</label>
                </div>

                <button type="submit" class="btn">Рассчитать</button>
            </form>

            <div id="result" class="result">
                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo htmlspecialchars($error) ?></div>
                <?php elseif ($success): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success) ?></div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 Калькулятор доставки. Все права защищены.</p>
        </div>
    </footer>
</body>

</html>