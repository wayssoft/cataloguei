unit uFinish;

{$mode ObjFPC}{$H+}

interface

uses
  Classes, SysUtils, Forms, Controls, Graphics, Dialogs, StdCtrls, ExtCtrls, ShellAPi;

type

  { TfrmFinish }

  TfrmFinish = class(TForm)
    Button1: TButton;
    Button2: TButton;
    Image1: TImage;
    Label1: TLabel;
    Label2: TLabel;
    Label3: TLabel;
    LbTotalSync: TLabel;
    LbTotalErro: TLabel;
    procedure Button1Click(Sender: TObject);
    procedure Button2Click(Sender: TObject);
    procedure FormShow(Sender: TObject);
  private

  public
    var
    TotError,
    TotSync:Integer;
    log:String;
  end;

var
  frmFinish: TfrmFinish;

implementation

{$R *.lfm}

{ TfrmFinish }

procedure TfrmFinish.Button1Click(Sender: TObject);
begin
  Close;
end;

procedure TfrmFinish.Button2Click(Sender: TObject);
begin
  //ShellExecute(handle,'open',PChar(log), '','')
  ShellExecute(Handle, PChar ('open'), PChar (log),
         PChar (''), PChar (''), 1);
end;

procedure TfrmFinish.FormShow(Sender: TObject);
begin
  LbTotalErro.Caption:=IntToStr(TotError);
  LbTotalSync.Caption:=IntToStr(TotSync);
end;

end.

