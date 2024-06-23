unit uConex;

{$mode ObjFPC}{$H+}

interface

uses
  Classes, SysUtils, DB, ZConnection, ZDataset;

type

  { TDM }

  TDM = class(TDataModule)
    qry_config: TZQuery;
    qry_configid: TLargeintField;
    qry_confignumero_whatsapp: TMemoField;
    qry_configsenha: TMemoField;
    qry_configtoken: TMemoField;
    qry_produtos_open_link: TZQuery;
    qry_produtosidentificador: TMemoField;
    qry_produtosid_produto_cataloguei: TLargeintField;
    qry_produtospath_img: TMemoField;
    qry_produtosstatus: TMemoField;
    qry_produtos_open_linkcodigo_barras: TMemoField;
    qry_produtos_open_linkdescricao: TMemoField;
    qry_produtos_open_linkid: TLargeintField;
    qry_produtos_open_linkidentificador: TMemoField;
    qry_produtos_open_linkid_produto_cataloguei: TLargeintField;
    qry_produtos_open_linknome: TMemoField;
    qry_produtos_open_linkpath_img: TMemoField;
    qry_produtos_open_linkpreco: TFloatField;
    qry_produtos_open_linkquantidade: TFloatField;
    qry_produtos_open_linkstatus: TMemoField;
    ZConnection1: TZConnection;
    qry_produtos: TZQuery;
    qry_produtoscodigo_barras: TMemoField;
    qry_produtosdescricao: TMemoField;
    qry_produtosid: TLargeintField;
    qry_produtosnome: TMemoField;
    qry_produtospreco: TFloatField;
    qry_produtosquantidade: TFloatField;
    ZTable1codigo_barras: TMemoField;
    ZTable1descricao: TMemoField;
    ZTable1id: TLargeintField;
    ZTable1identificador: TMemoField;
    ZTable1nome: TMemoField;
    ZTable1path_img: TMemoField;
    ZTable1preco: TFloatField;
    ZTable1quantidade: TFloatField;
  private

  public
    var
    dominio:String;
  end;

var
  DM: TDM;

implementation

{$R *.lfm}

end.

