async function carregarAlunosMatricula() {
  const resposta = await fetch(`${API_URL}/list/alunos`);
  const alunos = await resposta.json();

  const select = document.getElementById("matriculaAlunoId");
  select.innerHTML = `<option value="">Selecione o aluno</option>`;

  alunos.forEach(aluno => {
    select.innerHTML += `
      <option value="${aluno.id}">
        ${aluno.nome} - RGM: ${aluno.rgm}
      </option>
    `;
  });
}

async function carregarDisciplinasMatricula() {
  const resposta = await fetch(`${API_URL}/list/disciplinas`);
  const disciplinas = await resposta.json();

  const select = document.getElementById("matriculaDisciplinaId");
  select.innerHTML = `<option value="">Selecione a disciplina</option>`;

  disciplinas.forEach(disciplina => {
    select.innerHTML += `
      <option value="${disciplina.id}">
        ${disciplina.nome} - ${disciplina.codigo}
      </option>
    `;
  });
}

async function listarMatriculas() {
  const resposta = await fetch(`${API_URL}/aluno_disciplina`);
  const matriculas = await resposta.json();

  const tabela = document.getElementById("tabelaMatriculas");
  tabela.innerHTML = "";

  matriculas.forEach(matricula => {
    tabela.innerHTML += `
      <tr>
        <td>${matricula.id}</td>
        <td>${matricula.aluno_id}</td>
        <td>${matricula.disciplina_id}</td>
        <td>
          <button class="btn-excluir" onclick="excluirMatricula(${matricula.id})">Excluir</button>
        </td>
      </tr>
    `;
  });
}

document.getElementById("formMatricula").addEventListener("submit", async function(event) {
  event.preventDefault();

  const matricula = {
    aluno_id: Number(document.getElementById("matriculaAlunoId").value),
    disciplina_id: Number(document.getElementById("matriculaDisciplinaId").value)
  };

  const resposta = await fetch(`${API_URL}/aluno_disciplina`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(matricula)
  });

  if (!resposta.ok) {
    const erro = await resposta.json();
    alert(erro.erro || "Erro ao cadastrar matrícula");
    return;
  }

  this.reset();
  listarMatriculas();
});

async function excluirMatricula(id) {
  if (!confirm("Deseja excluir esta matrícula?")) return;

  await fetch(`${API_URL}/aluno_disciplina/${id}`, {
    method: "DELETE"
  });

  listarMatriculas();
}

carregarAlunosMatricula();
carregarDisciplinasMatricula();
listarMatriculas();