<html>

<head>
    <title>Moving Bird</title>
    <style>
        body {
    /* background-image: url('A.jpg');  linear-gradient(to left,pink,orange); */
    background-image: linear-gradient(to right, deeppink, orange);
    background-repeat: no-repeat;
    background-size: cover;
    height: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 0;
}
.card {
    width: 250px;
    height: 200px;
    background-color: rgba(160, 197, 226, 0.5);
    border-radius: 15px;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    padding: 100px;
    position: relative; /* Add position relative to the card */
}

button.open-button {
    margin-top: 25px;
    background-color: #34df34;
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 30px 40px;
    font-size: 30px;
    cursor: pointer;
    z-index: 9999;
    animation: pulsate 2s infinite;
}

button.open-button b {
    position: relative;
    z-index: 2;
}

@keyframes pulsate {
    0% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.1);
    }
    100% {
        transform: scale(1);
    }
}

/* Media queries for different screen sizes */
@media (max-width: 768px) {
    /* Styles for small devices (e.g., smartphones) */
    body {
        padding: 40px;
    }
}

@media (min-width: 769px) and (max-width: 1024px) {
    /* Styles for medium devices (e.g., tablets) */
    body {
        padding: 50px;
    }
}

@media (min-width: 1025px) {
    /* Styles for large devices (e.g., desktops) */
    body {
        max-width: 1200px;
        margin: 0 auto;
        padding: 60px;
    }
}




    </style>
</head>

<body>
    <div class="card">
        <h1><em>Moving Bird <3 </em></h1>
        <a href="group.php">
            <button class="open-button">
                <B><i>
                        Open
                    </i>

                </B>
            </button>
        </a>
    </div>

    <!-- completed the project -->
</body>

</html>