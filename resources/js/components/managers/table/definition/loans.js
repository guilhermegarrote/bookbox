export default {
    columns: [
        { key: "title", label: "Livro", sortable: true },
        { key: "number", label: "Exemplar", sortable: true },
        { key: "author", label: "Autor", sortable: true },
        { key: "name", label: "Aluno", sortable: true },
        { key: "loan_due_date", label: "Data de vencimento", sortable: true },
    ],
    actions: [
        { icon: "plus", width: "50px" },
    ],
    errorMessage: "Erro ao carregar empréstimos.",
    notFoundMessage: "Nenhum empréstimo foi encontrado.",
};
