async function carregarAlunosBoletim() {
  const resposta = await fetch(`${API_URL}/list/alunos`);
  const alunos = await resposta.json();

  const select = document.getElementById("boletimAlunoId");
  select.innerHTML = `<option value="">Selecione o aluno</option>`;

  alunos.forEach(aluno => {
    select.innerHTML += `
      <option value="${aluno.id}">
        ${aluno.nome} - RGM: ${aluno.rgm}
      </option>
    `;
  });
}

document.getElementById("formBoletim").addEventListener("submit", async function(event) {
  event.preventDefault();

  const alunoId = document.getElementById("boletimAlunoId").value;
  const resposta = await fetch(`${API_URL}/boletim/${alunoId}`);
  const dados = await resposta.json();

  const resultado = document.getElementById("resultadoBoletim");

  if (!resposta.ok) {
    resultado.innerHTML = `<p>${dados.erro || "Erro ao buscar boletim"}</p>`;
    return;
  }

  if (dados.disciplinas && dados.disciplinas.length === 0) {
    resultado.innerHTML = `<p>${dados.mensagem}</p>`;
    return;
  }

  let html = `
    <h3>Aluno: ${dados.aluno.nome}</h3>
    <p><strong>Email:</strong> ${dados.aluno.email}</p>
    <p><strong>RGM:</strong> ${dados.aluno.rgm}</p>

    <table>
      <thead>
        <tr>
          <th>Disciplina</th>
          <th>Código</th>
          <th>Nota 1</th>
          <th>Nota 2</th>
          <th>Média</th>
        </tr>
      </thead>
      <tbody>
  `;

  dados.disciplinas.forEach(item => {
    html += `
      <tr>
        <td>${item.nome}</td>
        <td>${item.codigo}</td>
        <td>${item.nota1 ?? "-"}</td>
        <td>${item.nota2 ?? "-"}</td>
        <td>${item.media ?? "-"}</td>
      </tr>
    `;
  });

  html += `
      </tbody>
    </table>
  `;

  resultado.innerHTML = html;
});

carregarAlunosBoletim();