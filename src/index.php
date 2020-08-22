<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" type="image/png" sizes="32x32" href="./img/favicon-32x32.png" />
    <title>your clock</title>
    <link href="https://fonts.googleapis.com/css2?family=Alfa+Slab+One&display=swap" rel="stylesheet" />

    <script>
    const vh = window.innerHeight * 0.01;
    document.documentElement.style.setProperty("--vh", `${vh}px`);
    const vw = window.innerWidth * 0.01;
    document.documentElement.style.setProperty("--vw", `${vw}px`);
    </script>

    <link rel="stylesheet" href="./css/styles.css" />
    <link rel="stylesheet" href="./css/micromodal.css" />
</head>

<body>
    <main>
        <header>
            <img src="./img/punch-away.png" alt="punch away" class="logo" />
            <div class="navigation">
                <a href="log-out.php">log out</a>
                <a href="#" data-micromodal-trigger="modal-preferences">preferences</a>
                <h1>home</h1>
            </div>
        </header>
        <form action="#">
            <div class="form">
                <div class="input-group">
                    <label for="month-select">📅 view month</label>
                    <select name="month-select" id="month-select">
                        <option value="0">08/2020</option>
                        <option value="1">07/2020</option>
                    </select>
                </div>
            </div>
        </form>
        <div class="table">
            <h2>august 2020</h2>
            <table>
                <thead>
                    <tr>
                        <th>📅</th>
                        <th>punched in</th>
                        <th>went out</th>
                        <th>got back</th>
                        <th>punched out</th>
                        <th>edit</th>
                        <th>day hours</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>17/08/2020</td>
                        <td>08:00</td>
                        <td>12:00</td>
                        <td>13:00</td>
                        <td>15:28</td>
                        <td>
                            <button data-micromodal-trigger="modal-date">
                                ✏️
                            </button>
                        </td>
                        <td>06:28</td>
                    </tr>

                    <tr>
                        <td>18/08/2020</td>
                        <td>08:00</td>
                        <td>12:00</td>
                        <td>13:00</td>
                        <td>15:28</td>
                        <td>
                            <button data-micromodal-trigger="modal-date">
                                ✏️
                            </button>
                        </td>
                        <td>06:28</td>
                    </tr>

                    <tr>
                        <td>19/08/2020</td>
                        <td>08:00</td>
                        <td>12:00</td>
                        <td>13:00</td>
                        <td>15:28</td>
                        <td>
                            <button data-micromodal-trigger="modal-date">
                                ✏️
                            </button>
                        </td>
                        <td>06:28</td>
                    </tr>

                    <tr>
                        <td>20/08/2020</td>
                        <td>08:00</td>
                        <td>12:00</td>
                        <td>13:00</td>
                        <td>15:28</td>
                        <td>
                            <button data-micromodal-trigger="modal-date">
                                ✏️
                            </button>
                        </td>
                        <td>06:28</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </main>
    <div class="modal micromodal-slide" id="modal-date" aria-hidden="true">
        <div class="modal__overlay" tabindex="-1" data-micromodal-close>
            <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-date-title">
                <header class="modal__header">
                    <h2 class="modal__title" id="modal-date-title">
                        punch clock | 17/08/2020
                    </h2>
                    <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
                </header>
                <main class="modal__content" id="modal-date-content">
                    <p>
                        Try hitting the tab key and notice how the focus stays within
                        the modal itself. Also, esc to close modal.
                    </p>
                </main>
                <footer class="modal__footer">
                    <button class="modal__btn modal__btn-primary">Continue</button>
                    <button class="modal__btn" data-micromodal-close aria-label="Close this dialog window">
                        Close
                    </button>
                </footer>
            </div>
        </div>
    </div>
    <div class="modal micromodal-slide" id="modal-preferences" aria-hidden="true">
        <div class="modal__overlay" tabindex="-1" data-micromodal-close>
            <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-preferences-title">
                <header class="modal__header">
                    <h2 class="modal__title" id="modal-preferences-title">
                        preferences
                    </h2>
                    <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
                </header>
                <main class="modal__content" id="modal-preferences-content">
                    <p>
                        Try hitting the tab key and notice how the focus stays within
                        the modal itself. Also, esc to close modal.
                    </p>
                </main>
                <footer class="modal__footer">
                    <button class="modal__btn modal__btn-primary">Continue</button>
                    <button class="modal__btn" data-micromodal-close aria-label="Close this dialog window">
                        Close
                    </button>
                </footer>
            </div>
        </div>
    </div>
</body>
<script src="./js/micromodal.min.js"></script>
<script>
window.onload = MicroModal.init();
</script>

</html>