import { route } from 'ziggy-js';
import { deleteStudent } from '@js/api/students/delete.js';
import { notifySuccess, notifyError } from '@js/utils/formErrors';
import { openUpdateModal } from './update';

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
                await deleteStudent(studentId);
                modalManager.removeModal('studentMenuModal');
                studentsTable.updateTable();
                notifySuccess('Aluno excluído com sucesso!');
            } catch (err) {
                notifyError(err.message || 'Erro ao excluir aluno');
            }
        });
    } catch (err) {
        console.error(err);
    }
}
