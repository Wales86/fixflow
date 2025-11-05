import { SVGProps } from 'react';

export const Illustration = (props: SVGProps<SVGSVGElement>) => (
    <svg
        viewBox="0 0 200 200"
        xmlns="http://www.w3.org/2000/svg"
        {...props}
    >
        <defs>
            <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop
                    offset="0%"
                    style={{ stopColor: 'var(--color-primary)', stopOpacity: 1 }}
                />
                <stop
                    offset="100%"
                    style={{
                        stopColor: 'var(--color-accent)',
                        stopOpacity: 1,
                    }}
                />
            </linearGradient>
        </defs>
        <path
            fill="url(#gradient)"
            d="M39.3,-52.8C52.1,-46.2,64.4,-36.8,71.1,-24.4C77.8,-11.9,78.8,3.6,73.5,16.8C68.2,30,56.5,41,44.2,50.8C31.9,60.6,18.9,69.2,3.9,72.2C-11.1,75.1,-24.1,72.4,-37.2,66.1C-50.3,59.8,-63.5,49.9,-70.7,37.1C-77.9,24.3,-79.1,8.6,-75.7,-5.7C-72.3,-20,-64.3,-32.9,-54.1,-40.7C-43.9,-48.5,-31.5,-51.2,-20.1,-55.5C-8.7,-59.8,--61.2,39.3,-52.8Z"
            transform="translate(100 100)"
        />
    </svg>
);
