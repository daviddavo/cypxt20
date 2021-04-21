#!/usr/bin/env python3
import os, sys
import jinja2
import pandas
import numpy as np

from jinja2 import Template

PLACEHOLDER_DICT = {
    "id": -1,
    "name": "Nombre",
    "from": "De",
    "age": "YY",
    "number": "91 234 56 78",
    "comment": "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla vestibulum nibh vel nibh scelerisque, sit amet interdum sapien venenatis. Nunc hendrerit, sem vel imperdiet mollis, nulla lectus imperdiet tellus, nec rutrum nibh ipsum id lorem. Nunc consectetur mauris nibh, vitae vehicula massa ornare vel."
}

WARNING_MAX_CHAR = 10

def getJinjaEnv():
    return jinja2.Environment(
        block_start_string = '\\BLOCK{',
        block_end_string = '}',
        variable_start_string = '\\VAR{',
        variable_end_string = '}',
        comment_start_string = '\\#{',
	comment_end_string = '}',
	line_statement_prefix = '%%',
	line_comment_prefix = '%#',
	trim_blocks = True,
	autoescape = False,
        loader = jinja2.FileSystemLoader(os.path.abspath("."))
    )

if __name__ == "__main__":
    if len(sys.argv) != 2:
        print(f'Usage: {sys.argv[0]} data.csv')
        sys.exit(1)

    env = getJinjaEnv()
    template = env.get_template('plantilla.tex')

    print(f"Loading csv data from {sys.argv[1]}")
    table = pandas.read_csv(sys.argv[1], index_col="id")
    table["from"] = table["from_name"]
    table["comment"].fillna('% Sin comentarios', inplace=True)
    table["name"].replace('(?i)^para\\s', '', inplace=True, regex=True)
    table["number"].replace('\\+34', '', inplace=True, regex=True)
    table["number"].replace('\\s', '', inplace=True, regex=True)
    print(f"Loaded {len(table)} entries")
    print(table)

    if not os.path.isdir('./build'):
        os.mkdir('./build')

    print(table.to_dict('index')[13])
    #  table = table[11:15]

    print("Generando Latex...")
    with open('./build/output.tex', 'w') as f:
        #  f.write(template.render(table=[PLACEHOLDER_DICT]))
        f.write(template.render(table=table.reset_index().to_dict('records')))

    print("Compilando Latex")

    os.chdir("./build")
    os.system("xelatex -interaction=batchmode output.tex")
    os.system("xelatex -interaction=batchmode output.tex")
