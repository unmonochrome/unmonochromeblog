import ClickSpark from './ClickSpark';
import './App.css'; // Mantém o estilo padrão que veio no projeto

function App() {
  return (
    <ClickSpark
      sparkColor="#ffffff"
      sparkSize={10}
      sparkRadius={15}
      sparkCount={8}
      duration={400}
    >
      {/* Tudo que ficar aqui dentro vai ter o efeito do clique por cima */}
      <div style={{ height: '100vh', display: 'flex', flexDirection: 'column', alignItems: 'center', justifyContent: 'center' }}>
        <h1>unmonochrome</h1>
        <p>Clique na tela para ver o efeito da documentação rodando!</p>
      </div>
    </ClickSpark>
  );
}

export default App;