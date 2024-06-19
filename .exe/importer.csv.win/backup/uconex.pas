unit uConex;

{$mode ObjFPC}{$H+}

interface

uses
  Classes, SysUtils, DB, ZConnection, ZDataset;

type

  { TDM }

  TDM = class(TDataModule)
    qry_produtosidentificador: TMemoField;
    qry_produtospath_img: TMemoField;
    ZConnection1: TZConnection;
    qry_produtos: TZQuery;
    qry_produtoscodigo_barras: TMemoField;
    qry_produtosdescricao: TMemoField;
    qry_produtosid: TLargeintField;
    qry_produtosnome: TMemoField;
    qry_produtospreco: TFloatField;
    qry_produtosquantidade: TFloatField;
    ZTable1: TZTable;
  private

  public

  end;

var
  DM: TDM;

implementation

{$R *.lfm}

end.

