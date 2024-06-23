unit uFiltro;

{$mode ObjFPC}{$H+}

interface

uses
  Classes, SysUtils, Forms, Controls, Graphics, Dialogs, StdCtrls, Buttons, uConex;

type

  { TfrmFiltro }

  TfrmFiltro = class(TForm)
    edtIdentificador: TEdit;
    edtCodigoBarras: TEdit;
    edtNome: TEdit;
    Label1: TLabel;
    Label2: TLabel;
    Label3: TLabel;
    SpeedButton1: TSpeedButton;
    SpeedButton2: TSpeedButton;
    SpeedButton3: TSpeedButton;
    SpeedButton4: TSpeedButton;
    procedure FormShow(Sender: TObject);
    procedure SpeedButton1Click(Sender: TObject);
    procedure SpeedButton2Click(Sender: TObject);
    procedure SpeedButton3Click(Sender: TObject);
    procedure SpeedButton4Click(Sender: TObject);
  private

  public

  end;

var
  frmFiltro: TfrmFiltro;

implementation

{$R *.lfm}

{ TfrmFiltro }

procedure TfrmFiltro.FormShow(Sender: TObject);
begin
  edtCodigoBarras.Clear;
  edtIdentificador.Clear;
  edtNome.Clear;
end;

procedure TfrmFiltro.SpeedButton1Click(Sender: TObject);
begin
  with DM.qry_produtos do
  begin
     Close;
     SQL.Clear;
     SQL.Add('SELECT * FROM produtos WHERE identificador = '+QuotedStr(edtIdentificador.Text));
     Open;
  end;
  Close;
end;

procedure TfrmFiltro.SpeedButton2Click(Sender: TObject);
begin
  with DM.qry_produtos do
  begin
     Close;
     SQL.Clear;
     SQL.Add('SELECT * FROM produtos WHERE codigo_barras = '+QuotedStr(edtCodigoBarras.Text));
     Open;
  end;
  Close;
end;

procedure TfrmFiltro.SpeedButton3Click(Sender: TObject);
begin
  with DM.qry_produtos do
  begin
     Close;
     SQL.Clear;
     SQL.Add('SELECT * FROM produtos WHERE nome like '+QuotedStr('%'+UpperCase(edtNome.Text)+'%'));
     Open;
  end;
  Close;
end;

procedure TfrmFiltro.SpeedButton4Click(Sender: TObject);
begin
  with DM.qry_produtos do
  begin
     Close;
     SQL.Clear;
     SQL.Add('SELECT * FROM produtos');
     Open;
  end;
  Close;
end;

end.

