async function listarAlunos() {
  const resposta = await fetch(`${API_URL}/list/alunos`);
  const alunos = await resposta.json();

  const tabela = document.getElementById("tabelaAlunos");
  tabela.innerHTML = "";

  alunos.forEach(aluno => {
    tabela.innerHTML += `
      <tr>
        <td>${aluno.id}</td>
        <td>${aluno.nome}</td>
        <td>${aluno.email}</td>
        <td>${aluno.rgm}</td>
        <td>
          <button class="btn-editar" onclick="editarAluno(${aluno.id}, '${aluno.nome}', '${aluno.email}', '${aluno.rgm}')">Editar</button>
          <button class="btn-excluir" onclick="excluirAluno(${aluno.id})">Excluir</button>
        </td>
      </tr>
    `;
  });
}

document.getElementById("formAluno").addEventListener("submit", async function(event) {
  event.preventDefault();

  const id = document.getElementById("alunoId").value;

  const aluno = {
    nome: document.getElementById("alunoNome").value,
    email: document.getElementById("alunoEmail").value,
    rgm: document.getElementById("alunoRgm").value
  };

  if (id) {
    await fetch(`${API_URL}/update/alunos/${id}`, {
      method: "PUT",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify(aluno)
    });
  } else {
    await fetch(`${API_URL}/create/alunos`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify(aluno)
    });
  }

  this.reset();
  document.getElementById("alunoId").value = "";
  listarAlunos();
});

function editarAluno(id, nome, email, rgm) {
  document.getElementById("alunoId").value = id;
  document.getElementById("alunoNome").value = nome;
  document.getElementById("alunoEmail").value = email;
  document.getElementById("alunoRgm").value = rgm;
}

async function excluirAluno(id) {
  const confirmar = confirm("Deseja excluir este aluno?");

  if (!confirmar) {
    return;
  }

  await fetch(`${API_URL}/delete/alunos/${id}`, {
    method: "DELETE"
  });

  listarAlunos();
}

listarAlunos();