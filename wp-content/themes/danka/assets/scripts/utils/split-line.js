import SplitText from '@activetheory/split-text';

export default function splitLine( element ) {
    const splitTextInstance = new SplitText(element, {
        type: 'lines, words',
    });

    splitTextInstance.lines.forEach(line => {
        // Par exemple, pour envelopper chaque ligne avec un <span>
        const wrapper = document.createElement('span');
        wrapper.classList.add('_y'); // vous pouvez ajouter une classe pour cibler le style via CSS
        
        // Insérer le wrapper juste avant la ligne
        line.parentNode.insertBefore(wrapper, line);
        
        // Déplacer la ligne dans le wrapper
        wrapper.appendChild(line);
    });
}