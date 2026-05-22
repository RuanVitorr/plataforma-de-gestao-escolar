async function carregarDisciplinasProfessor() {
  const resposta = await fetch(`${API_URL}/list/disciplinas`);
  const disciplinas = await resposta.json();

  const select = document.getElementById("professorDisciplinaId");
  select.innerHTML = `<option value="">Selecione a disciplina</option>`;

  disciplinas.forEach(disciplina => {
    select.innerHTML += `
      <option value="${disciplina.id}">
        ${disciplina.nome} - ${disciplina.codigo}
      </option>
    `;
  });
}

async function listarProfessores() {
  const resposta = await fetch(`${API_URL}/list/professores`);
  const professores = await resposta.json();

  const tabela = document.getElementById("tabelaProfessores");
  tabela.innerHTML = "";

  professores.forEach(professor => {
    tabela.innerHTML += `
      <tr>
        <td>${professor.id}</td>
        <td>${professor.nome}</td>
        <td>${professor.email}</td>
        <td>${professor.disciplina_id}</td>
        <td>
          <button class="btn-editar" onclick="editarProfessor(${professor.id}, '${professor.nome}', '${professor.email}', ${professor.disciplina_id})">Editar</button>
          <button class="btn-excluir" onclick="excluirProfessor(${professor.id})">Excluir</button>
        </td>
      </tr>
    `;
  });
}

document.getElementById("formProfessor").addEventListener("submit", async function(event) {
  event.preventDefault();

  const id = document.getElementById("professorId").value;

  const professor = {
    nome: document.getElementById("professorNome").value,
    email: document.getElementById("professorEmail").value,
    disciplina_id: Number(document.getElementById("professorDisciplinaId").value)
  };

  if (id) {
    await fetch(`${API_URL}/update/professores/${id}`, {
      method: "PUT",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(professor)
    });
  } else {
    await fetch(`${API_URL}/create/professores`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(professor)
    });
  }

  this.reset();
  document.getElementById("professorId").value = "";

  listarProfessores();
});

function editarProfessor(id, nome, email, disciplinaId) {
  document.getElementById("professorId").value = id;
  document.getElementById("professorNome").value = nome;
  document.getElementById("professorEmail").value = email;
  document.getElementById("professorDisciplinaId").value = disciplinaId;
}

async function excluirProfessor(id) {
  if (!confirm("Deseja excluir este professor?")) return;

  await fetch(`${API_URL}/delete/professores/${id}`, {
    method: "DELETE"
  });

  listarProfessores();
}

carregarDisciplinasProfessor();
listarProfessores();