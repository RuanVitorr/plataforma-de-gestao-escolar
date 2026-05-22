async function listarDisciplinas() {
  const resposta = await fetch(`${API_URL}/list/disciplinas`);
  const disciplinas = await resposta.json();

  const tabela = document.getElementById("tabelaDisciplinas");
  tabela.innerHTML = "";

  disciplinas.forEach(disciplina => {
    tabela.innerHTML += `
      <tr>
        <td>${disciplina.id}</td>
        <td>${disciplina.nome}</td>
        <td>${disciplina.codigo}</td>
        <td>
          <button class="btn-editar" onclick="editarDisciplina(${disciplina.id}, '${disciplina.nome}', '${disciplina.codigo}')">Editar</button>
          <button class="btn-excluir" onclick="excluirDisciplina(${disciplina.id})">Excluir</button>
        </td>
      </tr>
    `;
  });
}

document.getElementById("formDisciplina").addEventListener("submit", async function(event) {
  event.preventDefault();

  const id = document.getElementById("disciplinaId").value;

  const disciplina = {
    nome: document.getElementById("disciplinaNome").value,
    codigo: document.getElementById("disciplinaCodigo").value
  };

  if (id) {
    await fetch(`${API_URL}/update/disciplinas/${id}`, {
      method: "PUT",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(disciplina)
    });
  } else {
    await fetch(`${API_URL}/create/disciplinas`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(disciplina)
    });
  }

  this.reset();
  document.getElementById("disciplinaId").value = "";
  listarDisciplinas();
});

function editarDisciplina(id, nome, codigo) {
  document.getElementById("disciplinaId").value = id;
  document.getElementById("disciplinaNome").value = nome;
  document.getElementById("disciplinaCodigo").value = codigo;
}

async function excluirDisciplina(id) {
  if (!confirm("Deseja excluir esta disciplina?")) return;

  await fetch(`${API_URL}/delete/disciplinas/${id}`, {
    method: "DELETE"
  });

  listarDisciplinas();
}

listarDisciplinas();