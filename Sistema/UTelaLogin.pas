unit UTelaLogin;

interface

uses
  System.SysUtils, System.Types, System.UITypes, System.Classes, System.Variants,
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
  private
    { Private declarations }
  public
    { Public declarations }
  end;

var
  FrmLogin: TFrmLogin;

implementation

{$R *.fmx}

procedure TFrmLogin.BtnEntrarClick(Sender: TObject);
begin
  //Abrir próxima tela
end;

end.
