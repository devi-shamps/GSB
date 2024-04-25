document.getElementById('formUser').addEventListener('submit', function(e) {
    e.preventDefault();

    var userId = document.getElementById('user').value;

    fetch('/comptable/' + userId, )
        .then(response => response.json())
        .then(data => {
            var ficheFraisContainer = document.getElementById('ficheFraisContainer');
            // Clear the container

            // Show the container
            ficheFraisContainer.style.display = 'block';
        });
});