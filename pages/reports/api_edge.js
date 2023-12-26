let canvas = document.createElement("canvas");
let ctx = canvas.getContext("2d");

// Hacer una petición a la API de random user
fetch("https://randomuser.me/api/")
  .then((response) => response.json()) // Convertir la respuesta en JSON
  .then((data) => {
    // Obtener el primer usuario del resultado
    let user = data.results[0];

    // Obtener el nombre, apellido, foto y dirección del usuario
    let name = user.name.first + " " + user.name.last;
    let photo = user.picture.large;
    let address =
      user.location.street.name +
      " " +
      user.location.street.number +
      ", " +
      user.location.city +
      ", " +
      user.location.state +
      ", " +
      user.location.country;

    // Crear una imagen a partir de la foto del usuario
    let image = new Image();
    image.src = photo;
    image.onload = () => {
      // Dibujar la imagen en el canvas
      ctx.drawImage(image, 0, 0, 200, 200);

      // Establecer el estilo del texto
      ctx.font = "20px Arial";
      ctx.fillStyle = "black";

      // Dibujar el nombre y la dirección del usuario en el canvas
      ctx.fillText(name, 10, 220);
      ctx.fillText(address, 10, 250);

      // Añadir el canvas al documento
      document.body.appendChild(canvas);
    };
  })
  .catch((error) => {
    // Manejar el error
    console.error(error);
  });