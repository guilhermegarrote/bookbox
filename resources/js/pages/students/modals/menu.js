/**
 * Opens the "Student Menu" modal, providing actions for the selected student.
 *
 * @param {Object} modalManager - Modal controller instance
 * @param {number|string} studentId - ID of the student to manage
 * @param {Object} studentsTable - Table instance for updating after actions
 */
export async function openMenuModal(modalManager, studentId, studentsTable) {
    const url = route('students.menu-modal', { student: studentId });

    try {
        await modalManager.loadModalContent(url, 'studentMenuModal');

        document.getElementById('open-edit-modal')?.addEventListener('click', () => {
            openUpdateModal(modalManager, studentId, studentsTable);
        });

        document.getElementById('submit-delete')?.addEventListener('click', async () => {
            const confirmed = await modalManager.showModalMessage({
                message: 'Deseja realmente excluir este aluno?',
                acceptText: 'Sim',
                declineText: 'Cancelar'
            });

            if (!confirmed) return;

            try {
                const { ok, data: responseData } = await deleteStudent(studentId);

                if (!ok) {
                    notifyError(responseData?.error || 'Erro ao excluir aluno');
                } else {
                    modalManager.removeModal('studentMenuModal');
                    studentsTable.updateTable();
                    notifySuccess('Aluno excluído com sucesso!');
                }
            } catch (err) {
                console.error(err);
                notifyError('Erro técnico ao tentar excluir o aluno. Tente novamente.');
            }
        });
    } catch (err) {
        console.error(err);
    }
}
