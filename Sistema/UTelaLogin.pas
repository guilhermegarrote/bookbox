unit UTelaLogin;

interface

uses
  System.SysUtils, System.Types, System.UITypes, System.Classes,
  System.Variants,
  FMX.Types, FMX.Controls, FMX.Forms, FMX.Graphics, FMX.Dialogs, FMX.StdCtrls,
  FMX.Edit, FMX.Controls.Presentation, FMX.Layouts, FMX.Objects;

type
  TFrmLogin = class(TForm)
    Layout1: TLayout;
    RctFundoCinza: TRectangle;
    RctFundoBranco: TRectangle;
    Lblbookbox: TLabel;
    LblNome: TLabel;
    LblSenha: TLabel;
    RctBtnEntrar: TRectangle;
    BtnEntrar: TButton;
    RctNome: TRectangle;
    EdtNome: TEdit;
    RctSenha: TRectangle;
    EdtSenha: TEdit;
    procedure BtnEntrarClick(Sender: TObject);
    procedure EdtNomeKeyDown(Sender: TObject; var Key: Word; var KeyChar: Char;
      Shift: TShiftState);
  private
    { Private declarations }
  public
    { Public declarations }
  end;

var
  FrmLogin: TFrmLogin;

implementation

{$R *.fmx}

uses UModulo, UEmprestimos;

procedure TFrmLogin.BtnEntrarClick(Sender: TObject);
var
  nome: string;
  senha: string;
begin
  nome := Trim(EdtNome.Text);
  senha := Trim(EdtSenha.Text);

  if (nome = '') or (senha = '') then
  begin
    ShowMessage('Por favor, preencha nome de usuário e senha.');
    Exit;
  end;

  DataModuleBookBox.ConsultaUsuarios.Close;

  DataModuleBookBox.ConsultaUsuarios.SQL.Text := 'SELECT COUNT(*) AS UserCount FROM tbusuarios WHERE usuNome = :nome AND usuSenha = MD5(:senha)';
  DataModuleBookBox.ConsultaUsuarios.ParamByName('nome').AsString := nome;
  DataModuleBookBox.ConsultaUsuarios.ParamByName('senha').AsString := senha;

  try
    DataModuleBookBox.ConsultaUsuarios.Open;

    if DataModuleBookBox.ConsultaUsuarios.FieldByName('UserCount').AsInteger = 1
    then
    begin
      FrmEmprestimos.Show;
      FrmLogin.Hide;
    end
    else
    begin
      ShowMessage('Usuário ou senha incorretos!');
    end;

  except
    on E: Exception do
      ShowMessage('Erro ao acessar o banco de dados: ' + E.Message);
  end;

  DataModuleBookBox.ConsultaUsuarios.Close;
end;

procedure TFrmLogin.EdtNomeKeyDown(Sender: TObject; var Key: Word;
  var KeyChar: Char; Shift: TShiftState);
begin
  var
    VerificaEdt: string;
  begin
    VerificaEdt := EdtNome.Text;
    if KeyChar in [#8, #13, #27] then
    begin
      Exit;
    end;
    VerificaEdt := VerificaEdt + KeyChar;
    if not(KeyChar in ['A' .. 'Z', 'a' .. 'z', 'À' .. 'ÿ', '''', ' ']) then
    begin
      KeyChar := #0;
      Exit;
    end;

    if Pos('  ', VerificaEdt) > 0 then
    begin
      KeyChar := #0;
    end;

  end;
end;

end.
