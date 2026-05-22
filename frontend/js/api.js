const API_URL = "http://localhost:8000";

function mostrarSecao(id) {
  const secoes = document.querySelectorAll(".secao");

  secoes.forEach(secao => {
    secao.classList.remove("ativa");
  });

  document.getElementById(id).classList.add("ativa");
}