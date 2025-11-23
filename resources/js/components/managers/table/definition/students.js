export default {
    columns: [
        { key: "can_borrow", label: "Status", sortable: false },
        { key: "name", label: "Nome", sortable: true },
        { key: "email", label: "Email", sortable: false },
        { key: "phone", label: "Telefone", sortable: false },
        { key: "formatted_class_name", label: "Turma", sortable: true },
    ],
    actions: [
        { icon: "plus", width: "50px" },
    ],
    errorMessage: "Erro ao carregar alunos.",
    notFoundMessage: "Nenhum aluno foi encontrado.",
};
