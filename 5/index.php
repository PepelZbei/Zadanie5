<!DOCTYPE html>
<html>
<head>
    <title>Справочник регионов и городов</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <link href="jqui/jquery-ui.css" rel="stylesheet">
<script src="js/jquery-3.7.1.js"></script>
<script src="jqui/jquery-ui.js"></script>
<link href="assets/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php
    // Подключение к базе данных
    $conn=mysqli_connect("localhost","root","root","region");
    $error = '';

    // Добавление нового региона
    if (isset($_POST['add_region'])) {
        $region_name = $_POST['region_name'];

        if (!empty($region_name)) {
            $add_region_query = "INSERT INTO regions (name) VALUES ('$region_name')";
            mysqli_query($conn, $add_region_query);
        } else {
            $error = 'Введите название региона!';
        }
    }

    // Добавление нового города
    if (isset($_POST['add_city'])) {
        $city_name = $_POST['city_name'];
        $region_id = $_POST['region_id'];

        if (!empty($city_name) && !empty($region_id)) {
            $add_city_query = "INSERT INTO cities (name,region_id) VALUES ('$city_name', '$region_id')";
            mysqli_query($conn, $add_city_query);
        } else {
            $error = 'Введите название города и выберите регион!';
        }
    }

    // Получение списка регионов и городов
    $regions_query = "SELECT * FROM regions";
    $regions_result = mysqli_query($conn, $regions_query);

    echo '<h1>Справочник регионов и городов</h1>';

    if (!$regions_result) {
        echo 'Ошибка при получении данных из базы данных: ' . mysqli_error($conn);
    } else {
        echo '<h2>Регионы</h2>';
        echo '<form method="post" action="">
                <input type="text" name="region_name" placeholder="Название региона">
                <input type="submit" name="add_region" value="Добавить регион">
            </form>';

        echo '<table>
                <tr>
                    <th>Регион</th>
                    <th>Города</th>
                </tr>';

        while ($region_row = mysqli_fetch_assoc($regions_result)) {
            echo '<tr>
                    <td>' . $region_row['name'] . '</td>
                    <td>';

            $cities_query = "SELECT * FROM cities WHERE region_id = " . $region_row['id'];
            $cities_result = mysqli_query($conn, $cities_query);

            if (mysqli_num_rows($cities_result) > 0) {
                while ($city_row = mysqli_fetch_assoc($cities_result)) {
                    echo $city_row['name'] . '<br>';
                }
            } else {
                echo 'Нет городов';
            }

            echo '</td>
                </tr>';
        }

        echo '</table>';

        // Форма добавления города
        echo '<h2>Города</h2>';

        if (mysqli_num_rows($regions_result) > 0) {
            echo '<form method="post" action="">
                    <input type="text" name="city_name" placeholder="Название города">
                    <select name="region_id">';

            mysqli_data_seek($regions_result, 0);

            while ($region_row = mysqli_fetch_assoc($regions_result)) {
                echo '<option value="' . $region_row['id'] . '">' . $region_row['name'] . '</option>';
            }

            echo '</select>
                    <input type="submit" name="add_city" value="Добавить город">
                </form>';
        } else {
            echo 'Нет регионов';
        }

        // Отображение ошибок
        if (!empty($error)) {
            echo '<p class="error">' . $error;}
        }
        ?>