const initialState = {
  alunos: [],
  disciplinas: [],
  professores: [],
  matriculas: [],
  notas: [],
  nextId: {
    alunos: 1,
    disciplinas: 1,
    professores: 1,
    matriculas: 1,
    notas: 1
  }
};

let state = structuredClone(initialState);

const titles = {
  dashboard: "Painel",
  alunos: "Alunos",
  disciplinas: "Disciplinas",
  professores: "Professores",
  matriculas: "Matriculas",
  notas: "Notas",
  boletim: "Boletim"
};

const $ = (selector) => document.querySelector(selector);
const $$ = (selector) => Array.from(document.querySelectorAll(selector));

function clone(value) {
  return JSON.parse(JSON.stringify(value));
}

async function loadState() {
  const saved = window.schoolStore ? await window.schoolStore.read() : null;
  const local = localStorage.getItem("gestao-escolar-desktop");
  state = saved || (local ? JSON.parse(local) : structuredClone(initialState));
  normalizeState();
  render();
}

async function saveState() {
  normalizeState();
  if (window.schoolStore) {
    await window.schoolStore.write(state);
  }
  localStorage.setItem("gestao-escolar-desktop", JSON.stringify(state));
  render();
}

function normalizeState() {
  state = { ...clone(initialState), ...state, nextId: { ...initialState.nextId, ...(state.nextId || {}) } };
  for (const key of ["alunos", "disciplinas", "professores", "matriculas", "notas"]) {
    const maxId = state[key].reduce((max, item) => Math.max(max, Number(item.id) || 0), 0);
    state.nextId[key] = Math.max(Number(state.nextId[key]) || 1, maxId + 1);
  }
}

function createItem(collection, data) {
  const item = { id: state.nextId[collection]++, ...data };
  state[collection].push(item);
  return item;
}

function updateItem(collection, id, data) {
  const item = state[collection].find((record) => Number(record.id) === Number(id));
  if (item) Object.assign(item, data);
}

function deleteItem(collection, id) {
  state[collection] = state[collection].filter((item) => Number(item.id) !== Number(id));
}

function getAluno(id) {
  return state.alunos.find((aluno) => Number(aluno.id) === Number(id));
}

function getDisciplina(id) {
  return state.disciplinas.find((disciplina) => Number(disciplina.id) === Number(id));
}

function getProfessorByDisciplina(id) {
  return state.professores.find((professor) => Number(professor.disciplinaId) === Number(id));
}

function getMatricula(id) {
  return state.matriculas.find((matricula) => Number(matricula.id) === Number(id));
}

function getNotaByMatricula(matriculaId) {
  return state.notas.find((nota) => Number(nota.matriculaId) === Number(matriculaId));
}

function media(nota) {
  if (!nota) return "-";
  return ((Number(nota.nota1) + Number(nota.nota2)) / 2).toFixed(2);
}

function escapeHtml(value) {
  return String(value ?? "")
    .replaceAll("&", "&amp;")
    .replaceAll("<", "&lt;")
    .replaceAll(">", "&gt;")
    .replaceAll('"', "&quot;")
    .replaceAll("'", "&#039;");
}

function setView(viewId) {
  $$(".view").forEach((view) => view.classList.toggle("active", view.id === viewId));
  $$(".nav-item").forEach((button) => button.classList.toggle("active", button.dataset.view === viewId));
  $("#pageTitle").textContent = titles[viewId];
}

function optionList(items, selectedId, labelFn, emptyText) {
  const options = [`<option value="">${emptyText}</option>`];
  for (const item of items) {
    const selected = Number(item.id) === Number(selectedId) ? "selected" : "";
    options.push(`<option value="${item.id}" ${selected}>${escapeHtml(labelFn(item))}</option>`);
  }
  return options.join("");
}

function render() {
  renderMetrics();
  renderSelects();
  renderAlunos();
  renderDisciplinas();
  renderProfessores();
  renderMatriculas();
  renderNotas();
  renderBoletim();
}

function renderMetrics() {
  $("#metricAlunos").textContent = state.alunos.length;
  $("#metricDisciplinas").textContent = state.disciplinas.length;
  $("#metricProfessores").textContent = state.professores.length;
  $("#metricMatriculas").textContent = state.matriculas.length;
  $("#metricNotas").textContent = `${state.notas.length} notas lancadas`;

  const medias = state.notas.map((nota) => Number(media(nota))).filter(Number.isFinite);
  $("#metricMedia").textContent = medias.length
    ? (medias.reduce((total, value) => total + value, 0) / medias.length).toFixed(2)
    : "-";

  const alunosComBoletim = new Set(
    state.notas
      .map((nota) => getMatricula(nota.matriculaId))
      .filter(Boolean)
      .map((matricula) => matricula.alunoId)
  );
  $("#metricBoletins").textContent = alunosComBoletim.size;
  $("#metricSemProfessor").textContent = state.disciplinas.filter((disciplina) => !getProfessorByDisciplina(disciplina.id)).length;
}

function renderSelects() {
  $("#professorDisciplina").innerHTML = optionList(state.disciplinas, $("#professorDisciplina").value, (disciplina) => `${disciplina.nome} - ${disciplina.codigo}`, "Selecione a disciplina");
  $("#matriculaAluno").innerHTML = optionList(state.alunos, $("#matriculaAluno").value, (aluno) => `${aluno.nome} - ${aluno.rgm}`, "Selecione o aluno");
  $("#matriculaDisciplina").innerHTML = optionList(state.disciplinas, $("#matriculaDisciplina").value, (disciplina) => `${disciplina.nome} - ${disciplina.codigo}`, "Selecione a disciplina");
  $("#notaMatricula").innerHTML = optionList(state.matriculas, $("#notaMatricula").value, (matricula) => {
    const aluno = getAluno(matricula.alunoId);
    const disciplina = getDisciplina(matricula.disciplinaId);
    return `${aluno?.nome || "Aluno removido"} | ${disciplina?.nome || "Disciplina removida"}`;
  }, "Selecione a matricula");
  $("#boletimAluno").innerHTML = optionList(state.alunos, $("#boletimAluno").value, (aluno) => `${aluno.nome} - ${aluno.rgm}`, "Selecione o aluno");
}

function renderAlunos() {
  const tbody = $("#listaAlunos");
  if (!state.alunos.length) {
    tbody.innerHTML = `<tr><td colspan="4"><div class="empty">Nenhum aluno cadastrado.</div></td></tr>`;
    return;
  }

  tbody.innerHTML = state.alunos.map((aluno) => `
    <tr>
      <td>${escapeHtml(aluno.nome)}</td>
      <td>${escapeHtml(aluno.email)}</td>
      <td><span class="badge">${escapeHtml(aluno.rgm)}</span></td>
      <td class="action-row">
        <button type="button" data-edit-aluno="${aluno.id}">Editar</button>
        <button class="danger" type="button" data-delete-aluno="${aluno.id}">Excluir</button>
      </td>
    </tr>
  `).join("");
}

function renderDisciplinas() {
  const tbody = $("#listaDisciplinas");
  if (!state.disciplinas.length) {
    tbody.innerHTML = `<tr><td colspan="4"><div class="empty">Nenhuma disciplina cadastrada.</div></td></tr>`;
    return;
  }

  tbody.innerHTML = state.disciplinas.map((disciplina) => {
    const professor = getProfessorByDisciplina(disciplina.id);
    return `
      <tr>
        <td>${escapeHtml(disciplina.nome)}</td>
        <td><span class="badge">${escapeHtml(disciplina.codigo)}</span></td>
        <td>${escapeHtml(professor?.nome || "-")}</td>
        <td class="action-row">
          <button type="button" data-edit-disciplina="${disciplina.id}">Editar</button>
          <button class="danger" type="button" data-delete-disciplina="${disciplina.id}">Excluir</button>
        </td>
      </tr>
    `;
  }).join("");
}

function renderProfessores() {
  const tbody = $("#listaProfessores");
  if (!state.professores.length) {
    tbody.innerHTML = `<tr><td colspan="4"><div class="empty">Nenhum professor cadastrado.</div></td></tr>`;
    return;
  }

  tbody.innerHTML = state.professores.map((professor) => {
    const disciplina = getDisciplina(professor.disciplinaId);
    return `
      <tr>
        <td>${escapeHtml(professor.nome)}</td>
        <td>${escapeHtml(professor.email)}</td>
        <td>${escapeHtml(disciplina?.nome || "-")}</td>
        <td class="action-row">
          <button type="button" data-edit-professor="${professor.id}">Editar</button>
          <button class="danger" type="button" data-delete-professor="${professor.id}">Excluir</button>
        </td>
      </tr>
    `;
  }).join("");
}

function renderMatriculas() {
  const tbody = $("#listaMatriculas");
  if (!state.matriculas.length) {
    tbody.innerHTML = `<tr><td colspan="3"><div class="empty">Nenhuma matricula cadastrada.</div></td></tr>`;
    return;
  }

  tbody.innerHTML = state.matriculas.map((matricula) => {
    const aluno = getAluno(matricula.alunoId);
    const disciplina = getDisciplina(matricula.disciplinaId);
    return `
      <tr>
        <td>${escapeHtml(aluno?.nome || "Aluno removido")}</td>
        <td>${escapeHtml(disciplina?.nome || "Disciplina removida")}</td>
        <td class="action-row">
          <button class="danger" type="button" data-delete-matricula="${matricula.id}">Excluir</button>
        </td>
      </tr>
    `;
  }).join("");
}

function renderNotas() {
  const tbody = $("#listaNotas");
  if (!state.notas.length) {
    tbody.innerHTML = `<tr><td colspan="6"><div class="empty">Nenhuma nota lancada.</div></td></tr>`;
    return;
  }

  tbody.innerHTML = state.notas.map((nota) => {
    const matricula = getMatricula(nota.matriculaId);
    const aluno = matricula ? getAluno(matricula.alunoId) : null;
    const disciplina = matricula ? getDisciplina(matricula.disciplinaId) : null;
    const mediaFinal = Number(media(nota));
    const status = mediaFinal >= 6 ? "success" : "warning";
    return `
      <tr>
        <td>${escapeHtml(aluno?.nome || "Aluno removido")}</td>
        <td>${escapeHtml(disciplina?.nome || "Disciplina removida")}</td>
        <td>${Number(nota.nota1).toFixed(1)}</td>
        <td>${Number(nota.nota2).toFixed(1)}</td>
        <td><span class="badge ${status}">${media(nota)}</span></td>
        <td class="action-row">
          <button type="button" data-edit-nota="${nota.id}">Editar</button>
          <button class="danger" type="button" data-delete-nota="${nota.id}">Excluir</button>
        </td>
      </tr>
    `;
  }).join("");
}

function renderBoletim() {
  const alunoId = $("#boletimAluno").value;
  const result = $("#boletimResultado");
  if (!alunoId) {
    result.className = "boletim-empty";
    result.innerHTML = "Selecione um aluno para visualizar as notas.";
    return;
  }

  const aluno = getAluno(alunoId);
  const matriculas = state.matriculas.filter((matricula) => Number(matricula.alunoId) === Number(alunoId));
  if (!matriculas.length) {
    result.className = "boletim-empty";
    result.innerHTML = "Este aluno ainda nao possui matriculas.";
    return;
  }

  result.className = "boletim-card";
  result.innerHTML = `
    <div class="boletim-head">
      <div>
        <h2>${escapeHtml(aluno.nome)}</h2>
        <p>${escapeHtml(aluno.email)} | RGM ${escapeHtml(aluno.rgm)}</p>
      </div>
      <span class="badge">${matriculas.length} disciplinas</span>
    </div>
    <table>
      <thead><tr><th>Disciplina</th><th>Codigo</th><th>N1</th><th>N2</th><th>Media</th></tr></thead>
      <tbody>
        ${matriculas.map((matricula) => {
          const disciplina = getDisciplina(matricula.disciplinaId);
          const nota = getNotaByMatricula(matricula.id);
          return `
            <tr>
              <td>${escapeHtml(disciplina?.nome || "Disciplina removida")}</td>
              <td>${escapeHtml(disciplina?.codigo || "-")}</td>
              <td>${nota ? Number(nota.nota1).toFixed(1) : "-"}</td>
              <td>${nota ? Number(nota.nota2).toFixed(1) : "-"}</td>
              <td>${nota ? media(nota) : "-"}</td>
            </tr>
          `;
        }).join("")}
      </tbody>
    </table>
  `;
}

function bindNavigation() {
  $$(".nav-item").forEach((button) => {
    button.addEventListener("click", () => setView(button.dataset.view));
  });
}

function bindForms() {
  $("#formAluno").addEventListener("submit", async (event) => {
    event.preventDefault();
    const id = $("#alunoId").value;
    const data = {
      nome: $("#alunoNome").value.trim(),
      email: $("#alunoEmail").value.trim(),
      rgm: $("#alunoRgm").value.trim()
    };
    id ? updateItem("alunos", id, data) : createItem("alunos", data);
    event.target.reset();
    $("#alunoId").value = "";
    await saveState();
  });

  $("#formDisciplina").addEventListener("submit", async (event) => {
    event.preventDefault();
    const id = $("#disciplinaId").value;
    const data = {
      nome: $("#disciplinaNome").value.trim(),
      codigo: $("#disciplinaCodigo").value.trim().toUpperCase()
    };
    id ? updateItem("disciplinas", id, data) : createItem("disciplinas", data);
    event.target.reset();
    $("#disciplinaId").value = "";
    await saveState();
  });

  $("#formProfessor").addEventListener("submit", async (event) => {
    event.preventDefault();
    const id = $("#professorId").value;
    const data = {
      nome: $("#professorNome").value.trim(),
      email: $("#professorEmail").value.trim(),
      disciplinaId: Number($("#professorDisciplina").value)
    };
    id ? updateItem("professores", id, data) : createItem("professores", data);
    event.target.reset();
    $("#professorId").value = "";
    await saveState();
  });

  $("#formMatricula").addEventListener("submit", async (event) => {
    event.preventDefault();
    const alunoId = Number($("#matriculaAluno").value);
    const disciplinaId = Number($("#matriculaDisciplina").value);
    const exists = state.matriculas.some((matricula) => matricula.alunoId === alunoId && matricula.disciplinaId === disciplinaId);
    if (exists) {
      alert("Este aluno ja esta matriculado nesta disciplina.");
      return;
    }
    createItem("matriculas", { alunoId, disciplinaId });
    event.target.reset();
    await saveState();
  });

  $("#formNota").addEventListener("submit", async (event) => {
    event.preventDefault();
    const id = $("#notaId").value;
    const matriculaId = Number($("#notaMatricula").value);
    const data = {
      matriculaId,
      nota1: Number($("#nota1").value),
      nota2: Number($("#nota2").value)
    };
    const existing = state.notas.find((nota) => nota.matriculaId === matriculaId && Number(nota.id) !== Number(id));
    if (existing) {
      alert("Esta matricula ja possui notas. Edite o lancamento existente.");
      return;
    }
    id ? updateItem("notas", id, data) : createItem("notas", data);
    event.target.reset();
    $("#notaId").value = "";
    await saveState();
  });
}

function bindTableActions() {
  document.body.addEventListener("click", async (event) => {
    const button = event.target.closest("button");
    if (!button) return;

    if (button.dataset.editAluno) editAluno(button.dataset.editAluno);
    if (button.dataset.editDisciplina) editDisciplina(button.dataset.editDisciplina);
    if (button.dataset.editProfessor) editProfessor(button.dataset.editProfessor);
    if (button.dataset.editNota) editNota(button.dataset.editNota);
    if (button.dataset.deleteAluno) await removeAluno(button.dataset.deleteAluno);
    if (button.dataset.deleteDisciplina) await removeDisciplina(button.dataset.deleteDisciplina);
    if (button.dataset.deleteProfessor) await removeProfessor(button.dataset.deleteProfessor);
    if (button.dataset.deleteMatricula) await removeMatricula(button.dataset.deleteMatricula);
    if (button.dataset.deleteNota) await removeNota(button.dataset.deleteNota);
  });
}

function editAluno(id) {
  const aluno = getAluno(id);
  $("#alunoId").value = aluno.id;
  $("#alunoNome").value = aluno.nome;
  $("#alunoEmail").value = aluno.email;
  $("#alunoRgm").value = aluno.rgm;
}

function editDisciplina(id) {
  const disciplina = getDisciplina(id);
  $("#disciplinaId").value = disciplina.id;
  $("#disciplinaNome").value = disciplina.nome;
  $("#disciplinaCodigo").value = disciplina.codigo;
}

function editProfessor(id) {
  const professor = state.professores.find((item) => Number(item.id) === Number(id));
  $("#professorId").value = professor.id;
  $("#professorNome").value = professor.nome;
  $("#professorEmail").value = professor.email;
  $("#professorDisciplina").value = professor.disciplinaId;
}

function editNota(id) {
  const nota = state.notas.find((item) => Number(item.id) === Number(id));
  $("#notaId").value = nota.id;
  $("#notaMatricula").value = nota.matriculaId;
  $("#nota1").value = nota.nota1;
  $("#nota2").value = nota.nota2;
}

async function removeAluno(id) {
  if (!confirm("Excluir este aluno e seus vinculos?")) return;
  const matriculas = state.matriculas.filter((matricula) => Number(matricula.alunoId) === Number(id)).map((matricula) => matricula.id);
  deleteItem("alunos", id);
  state.matriculas = state.matriculas.filter((matricula) => Number(matricula.alunoId) !== Number(id));
  state.notas = state.notas.filter((nota) => !matriculas.includes(nota.matriculaId));
  await saveState();
}

async function removeDisciplina(id) {
  if (!confirm("Excluir esta disciplina e seus vinculos?")) return;
  const matriculas = state.matriculas.filter((matricula) => Number(matricula.disciplinaId) === Number(id)).map((matricula) => matricula.id);
  deleteItem("disciplinas", id);
  state.professores = state.professores.filter((professor) => Number(professor.disciplinaId) !== Number(id));
  state.matriculas = state.matriculas.filter((matricula) => Number(matricula.disciplinaId) !== Number(id));
  state.notas = state.notas.filter((nota) => !matriculas.includes(nota.matriculaId));
  await saveState();
}

async function removeProfessor(id) {
  if (!confirm("Excluir este professor?")) return;
  deleteItem("professores", id);
  await saveState();
}

async function removeMatricula(id) {
  if (!confirm("Excluir esta matricula e suas notas?")) return;
  deleteItem("matriculas", id);
  state.notas = state.notas.filter((nota) => Number(nota.matriculaId) !== Number(id));
  await saveState();
}

async function removeNota(id) {
  if (!confirm("Excluir esta nota?")) return;
  deleteItem("notas", id);
  await saveState();
}

function bindUtilityActions() {
  $("#boletimAluno").addEventListener("change", renderBoletim);
  $("#seedButton").addEventListener("click", async () => {
    state = createSeedState();
    await saveState();
  });
  $("#resetButton").addEventListener("click", async () => {
    if (!confirm("Limpar todos os dados cadastrados?")) return;
    state = structuredClone(initialState);
    await saveState();
  });
}

function createSeedState() {
  state = structuredClone(initialState);
  const ana = createItem("alunos", { nome: "Ana Beatriz Lima", email: "ana.lima@email.com", rgm: "251001" });
  const pedro = createItem("alunos", { nome: "Pedro Henrique Souza", email: "pedro.souza@email.com", rgm: "251002" });
  const matematica = createItem("disciplinas", { nome: "Matematica Aplicada", codigo: "MAT101" });
  const banco = createItem("disciplinas", { nome: "Banco de Dados", codigo: "BD201" });
  createItem("professores", { nome: "Marina Costa", email: "marina.costa@email.com", disciplinaId: matematica.id });
  createItem("professores", { nome: "Carlos Mendes", email: "carlos.mendes@email.com", disciplinaId: banco.id });
  const matriculaAna = createItem("matriculas", { alunoId: ana.id, disciplinaId: matematica.id });
  const matriculaPedro = createItem("matriculas", { alunoId: pedro.id, disciplinaId: banco.id });
  createItem("notas", { matriculaId: matriculaAna.id, nota1: 8.5, nota2: 9.0 });
  createItem("notas", { matriculaId: matriculaPedro.id, nota1: 6.8, nota2: 7.4 });
  return state;
}

bindNavigation();
bindForms();
bindTableActions();
bindUtilityActions();
loadState();
