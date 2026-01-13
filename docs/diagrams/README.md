Mermaid DFD — README

This folder contains a Mermaid template for a Data Flow Diagram and instructions to preview or export it.

Recommended ways to preview:

- In VS Code: install the "Markdown Preview Mermaid Support" or "Mermaid Markdown Preview" extension, then open `dfd-template.md` and toggle the Markdown preview.

- CLI export with mermaid-cli (recommended for SVG/PNG):

```bash
# install once
npm install -g @mermaid-js/mermaid-cli

# render to SVG
mmdc -i docs/diagrams/dfd-template.md -o docs/diagrams/data-flow.svg
```

Tips:
- Edit `dfd-template.md` to add your system-specific entities and flows.
- Provide me the list of components (actors, services, data stores) and the interactions between them and I'll update the diagram for you.