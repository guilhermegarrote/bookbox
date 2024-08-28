program bookbox;

uses
  System.StartUpCopy,
  FMX.Forms,
  UTelaLogin in 'UTelaLogin.pas' {FrmLogin};

{$R *.res}

begin
  Application.Initialize;
  Application.CreateForm(TFrmLogin, FrmLogin);
  Application.Run;
end.
