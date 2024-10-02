program bookbox;

uses
  System.StartUpCopy,
  FMX.Forms,
  UTelaLogin in 'UTelaLogin.pas' {FrmLogin},
  UModulo in 'UModulo.pas' {DataModuleBookBox: TDataModule},
  UEmprestimos in 'UEmprestimos.pas' {FrmEmprestimos};

{$R *.res}

begin
  Application.Initialize;
  Application.CreateForm(TFrmLogin, FrmLogin);
  Application.CreateForm(TDataModuleBookBox, DataModuleBookBox);
  Application.CreateForm(TFrmEmprestimos, FrmEmprestimos);
  Application.Run;
end.
