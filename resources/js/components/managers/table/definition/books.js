export default {
    columns: [
        { key: "available_copies", label: "Disponíveis", sortable: true },
        { key: "isbn", label: "ISBN" },
        { key: "title", label: "Título", sortable: true },
        { key: "author", label: "Autor", sortable: true },
        { key: "genre_name", label: "Gênero", sortable: true },
        { key: "publisher", label: "Editora", sortable: true },
    ],
    actions: [
        { icon: "label", width: "30px" },
        { icon: "plus", width: "50px" }
    ],
    errorMessage: "Erro ao carregar livros.",
    notFoundMessage: "Nenhum livro foi encontrado.",
};
