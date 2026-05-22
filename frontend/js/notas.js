let alunosCache = [];
let disciplinasCache = [];
let matriculasCache = [];

function primeiroNome(nomeCompleto) {
  return nomeCompleto.split(" ")[0];
}

function calcularMedia(nota1, nota2) {
  return ((Number(nota1) + Number(nota2)) / 2).toFixed(2);
}

async function carregarDadosNotas() {
  const respostaAlunos = await fetch(`${API_URL}/list/alunos`);
  alunosCache = await respostaAlunos.json();

  const respostaDisciplinas = await fetch(`${API_URL}/list/disciplinas`);
  disciplinasCache = await respostaDisciplinas.json();

  const respostaMatriculas = await fetch(`${API_URL}/aluno_disciplina`);
  matriculasCache = await respostaMatriculas.json();

  const select = document.getElementById("notaMatriculaId");
  select.innerHTML = `<option value="">Selecione a matrícula</option>`;

  matriculasCache.forEach(matricula => {
    const aluno = alunosCache.find(a => Number(a.id) === Number(matricula.aluno_id));
    const disciplina = disciplinasCache.find(d => Number(d.id) === Number(matricula.disciplina_id));

    const nomeAluno = aluno ? primeiroNome(aluno.nome) : `Aluno ${matricula.aluno_id}`;
    const nomeDisciplina = disciplina ? disciplina.nome : `Disciplina ${matricula.disciplina_id}`;

    select.innerHTML += `
      <option value="${matricula.id}">
        Matrícula ${matricula.id} | ${nomeAluno} | ${nomeDisciplina}
      </option>
    `;
  });
}

async function listarNotas() {
  const resposta = await fetch(`${API_URL}/notas`);
  const notas = await resposta.json();

  const tabela = document.getElementById("tabelaNotas");
  tabela.innerHTML = "";

  notas.forEach(nota => {
    const matricula = matriculasCache.find(m => Number(m.id) === Number(nota.aluno_disciplina_id));

    let nomeAluno = "-";

    if (matricula) {
      const aluno = alunosCache.find(a => Number(a.id) === Number(matricula.aluno_id));
      nomeAluno = aluno ? primeiroNome(aluno.nome) : "-";
    }

    tabela.innerHTML += `
      <tr>
        <td>${nota.id}</td>
        <td>${nota.aluno_disciplina_id}</td>
        <td>${nomeAluno}</td>
        <td>${nota.nota1}</td>
        <td>${nota.nota2}</td>
        <td>${calcularMedia(nota.nota1, nota.nota2)}</td>
        <td>
          <button
            class="btn-editar"
            onclick="editarNota(${nota.id}, ${nota.aluno_disciplina_id}, ${nota.nota1}, ${nota.nota2})"
          >
            Editar
          </button>

          <button
            class="btn-excluir"
            onclick="excluirNota(${nota.id})"
          >
            Excluir
          </button>
        </td>
      </tr>
    `;
  });
}

document.getElementById("formNota").addEventListener("submit", async function(event) {
  event.preventDefault();

  const id = document.getElementById("notaId").value;

  const nota = {
    aluno_disciplina_id: Number(document.getElementById("notaMatriculaId").value),
    nota1: Number(document.getElementById("nota1").value),
    nota2: Number(document.getElementById("nota2").value)
  };

  let resposta;

  if (id) {
    resposta = await fetch(`${API_URL}/notas/${id}`, {
      method: "PUT",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        nota1: nota.nota1,
        nota2: nota.nota2
      })
    });
  } else {
    resposta = await fetch(`${API_URL}/notas`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(nota)
    });
  }

  if (!resposta.ok) {
    const erro = await resposta.json();
    alert(erro.erro || "Erro ao salvar nota");
    return;
  }

  this.reset();
  document.getElementById("notaId").value = "";

  await carregarDadosNotas();
  listarNotas();
});

function editarNota(id, matriculaId, nota1, nota2) {
  document.getElementById("notaId").value = id;
  document.getElementById("notaMatriculaId").value = matriculaId;
  document.getElementById("nota1").value = nota1;
  document.getElementById("nota2").value = nota2;
}

async function excluirNota(id) {
  if (!confirm("Deseja excluir esta nota?")) return;

  await fetch(`${API_URL}/notas/${id}`, {
    method: "DELETE"
  });

  listarNotas();
}

async function iniciarNotas() {
  await carregarDadosNotas();
  listarNotas();
}

iniciarNotas();